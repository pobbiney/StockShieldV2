<?php

namespace App\Services;

use App\Models\ApproveStock;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\SatelliteStockReceipt;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockReceiptService
{
    public function __construct(
        protected StoreContext $storeContext
    ) {}

    public function assertSatelliteReceiptContext(?User $user = null): Store
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            throw new RuntimeException('You must be logged in to receive stock.');
        }

        $activeStore = $this->storeContext->getActiveStore();

        if (!$activeStore) {
            throw new RuntimeException('Please select your active store before receiving stock.');
        }

        if ($activeStore->store_group !== 'satellite') {
            throw new RuntimeException('Receive Stock is only available for satellite stores.');
        }

        if (!$this->storeContext->canAccessStore($user, (int) $activeStore->id)) {
            throw new RuntimeException('You do not have access to this store.');
        }

        return $activeStore;
    }

    /**
     * @param  Collection<int, ItemIssue>|array<int, ItemIssue>  $issues
     */
    public function acceptIssues(Collection|array $issues, ?User $receiver = null): int
    {
        $receiver = $receiver ?? auth()->user();
        $activeStore = $this->assertSatelliteReceiptContext($receiver);

        $issues = collect($issues)->filter(fn (ItemIssue $issue) => $this->isAwaitingReceipt($issue));

        if ($issues->isEmpty()) {
            return 0;
        }

        foreach ($issues as $issue) {
            if ((int) $issue->issue_to !== (int) $activeStore->id) {
                throw new RuntimeException('One or more items are not addressed to your active store.');
            }
        }

        $accepted = 0;

        DB::transaction(function () use ($issues, $receiver, &$accepted) {
            foreach ($issues as $issue) {
                if (!$this->isAwaitingReceipt($issue)) {
                    continue;
                }

                if (SatelliteStockReceipt::where('item_issue_id', $issue->id)->exists()) {
                    continue;
                }

                $this->acceptSingleIssue($issue, $receiver);
                $accepted++;
            }

            $this->syncItemRequestsForIssues($issues);
        });

        return $accepted;
    }

    public function isAwaitingReceipt(ItemIssue $issue): bool
    {
        return $issue->status === 'issued' && $issue->status_two === 'issued';
    }

    protected function acceptSingleIssue(ItemIssue $issue, User $receiver): void
    {
        $qty = (int) $issue->qty;

        if ($qty <= 0) {
            throw new RuntimeException('Cannot receive an issue line with zero quantity.');
        }

        $metadata = $this->resolveBatchMetadata($issue);
        $satelliteStoreId = (int) $issue->issue_to;

        SatelliteStockReceipt::create([
            'source_type'        => 'issue_transfer',
            'item_issue_id'      => $issue->id,
            'item_request_id'    => $issue->item_request_id,
            'stock_id'           => $issue->stock_id,
            'item_id'            => $issue->item_id,
            'batch_number'       => $issue->batch_number,
            'qty'                => $qty,
            'amount'             => $issue->amount ?? $metadata['amount'],
            'expiry_date'        => $metadata['expiry_date'],
            'purchase_order'     => $metadata['purchase_order'],
            'supplier_id'        => $metadata['supplier_id'],
            'store_id'           => $satelliteStoreId,
            'central_store_id'   => $issue->store_id,
            'requisition_no'     => $issue->requisition_no,
            'invoice_number'     => $issue->invoice_number,
            'received_by'        => $receiver->id,
            'received_at'        => now(),
        ]);

        $issue->update([
            'status'      => 'received',
            'status_two'  => 'received',
            'received_at' => now(),
            'received_by' => $receiver->id,
        ]);
    }

    /**
     * @return array{expiry_date: ?string, amount: float, purchase_order: ?string, supplier_id: ?int}
     */
    protected function resolveBatchMetadata(ItemIssue $issue): array
    {
        $centralBatch = ApproveStock::where('item_id', $issue->item_id)
            ->where('batch_number', $issue->batch_number)
            ->where('store_id', $issue->store_id)
            ->orderByDesc('id')
            ->first();

        if ($centralBatch) {
            return [
                'expiry_date'    => $centralBatch->expiry_date,
                'amount'         => (float) ($issue->amount ?? $centralBatch->amount ?? 0),
                'purchase_order' => $centralBatch->purchase_order,
                'supplier_id'    => $centralBatch->supplier_id,
            ];
        }

        $stock = Stock::find($issue->stock_id);

        if ($stock) {
            return [
                'expiry_date'    => $stock->expiry_date,
                'amount'         => (float) ($issue->amount ?? $stock->amount ?? 0),
                'purchase_order' => $stock->purchase_order,
                'supplier_id'    => $stock->supplier_id ? (int) $stock->supplier_id : null,
            ];
        }

        return [
            'expiry_date'    => null,
            'amount'         => (float) ($issue->amount ?? 0),
            'purchase_order' => null,
            'supplier_id'    => null,
        ];
    }

    /**
     * @param  Collection<int, ItemIssue>  $issues
     */
    protected function syncItemRequestsForIssues(Collection $issues): void
    {
        $requestIds = $issues->pluck('item_request_id')->filter()->unique();

        foreach ($requestIds as $requestId) {
            $itemRequest = ItemRequest::find($requestId);

            if (!$itemRequest) {
                continue;
            }

            $pendingIssues = ItemIssue::where('item_request_id', $requestId)
                ->where(function ($query) {
                    $query->where('status', 'pending')
                        ->orWhere(function ($q) {
                            $q->where('status', 'issued')->where('status_two', 'issued');
                        });
                })
                ->exists();

            if ($pendingIssues) {
                continue;
            }

            $receivedQty = (int) ItemIssue::where('item_request_id', $requestId)
                ->where('status', 'received')
                ->sum('qty');

            $itemRequest->update([
                'qty_issued' => $receivedQty > 0 ? $receivedQty : $itemRequest->qty_issued,
                'status'     => 'received',
            ]);
        }
    }

    public function pendingReceiptCount(?int $satelliteStoreId = null): int
    {
        if (!$satelliteStoreId) {
            return 0;
        }

        return ItemIssue::where('issue_to', $satelliteStoreId)
            ->where('status', 'issued')
            ->where('status_two', 'issued')
            ->count();
    }
}
