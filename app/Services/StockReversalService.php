<?php

namespace App\Services;

use App\Models\ApproveStock;
use App\Models\Stock;
use App\Models\StockReversal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockReversalService
{
    public const APPROVAL_ROUTE_NAMES = [
        'reverseEntryApproval',
        'stockApproval',
    ];

    public const APPROVAL_PAGE_ID_SUBS = [
        'reverse-entry-approval',
        'stock-approval',
    ];

    public function __construct(
        protected NotificationService $notifications,
        protected StoreContext $storeContext,
    ) {}

    public function canApproveReversal(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        foreach (self::APPROVAL_ROUTE_NAMES as $routeName) {
            if ($user->canAccessLinkRoute($routeName)) {
                return true;
            }
        }

        foreach (self::APPROVAL_PAGE_ID_SUBS as $pageIdSub) {
            if ($user->canAccessScreen($pageIdSub)) {
                return true;
            }
        }

        return false;
    }

    public function findReversibleBatch(string $batchNumber, ?array $scopedStoreIds = null): ?ApproveStock
    {
        $scopedStoreIds = $scopedStoreIds ?? $this->storeContext->getScopedStoreIds();

        if (empty($scopedStoreIds)) {
            return null;
        }

        return ApproveStock::with(['itemname', 'itemcode', 'storename'])
            ->where('batch_number', trim($batchNumber))
            ->whereIn('store_id', $scopedStoreIds)
            ->where('status', 'approved')
            ->where('qty', '>', 0)
            ->first();
    }

    public function submit(
        string $batchNumber,
        string $reversalType,
        ?int $qty,
        string $reason,
        ?User $requester = null
    ): StockReversal {
        $requester = $requester ?? auth()->user();

        if (!$requester) {
            throw new InvalidArgumentException('You must be logged in to submit a reversal.');
        }

        $batchNumber = trim($batchNumber);
        $approveStock = $this->findReversibleBatch($batchNumber);

        if (!$approveStock) {
            throw new InvalidArgumentException('Batch not found or is not available for reversal.');
        }

        if ($approveStock->status === 'return initiated') {
            throw new InvalidArgumentException('This batch has a return in progress.');
        }

        if (StockReversal::where('batch_number', $batchNumber)
            ->where('store_id', $approveStock->store_id)
            ->where('status', StockReversal::STATUS_PENDING)
            ->exists()) {
            throw new InvalidArgumentException('A pending reversal already exists for this batch.');
        }

        $availableQty = (int) $approveStock->qty;
        $qtyToReverse = $this->resolveQtyToReverse($reversalType, $qty, $availableQty);

        if ($qtyToReverse <= 0) {
            throw new InvalidArgumentException('Quantity to reverse must be greater than zero.');
        }

        if ($qtyToReverse > $availableQty) {
            throw new InvalidArgumentException('Quantity exceeds available batch stock.');
        }

        $reversal = StockReversal::create([
            'item_id'          => $approveStock->item_id,
            'batch_number'     => $batchNumber,
            'stock_id'         => $approveStock->stock_id,
            'approve_stock_id' => $approveStock->id,
            'store_id'         => $approveStock->store_id,
            'reversal_type'    => $reversalType,
            'qty_to_reverse'   => $qtyToReverse,
            'reason'           => trim($reason),
            'status'           => StockReversal::STATUS_PENDING,
            'requested_by'     => $requester->id,
        ]);

        $itemName = $approveStock->itemname->name ?? 'Item';

        $this->notifications->notifyUsersForAction(
            NotificationService::TYPE_REVERSE_ENTRY_PENDING_APPROVAL,
            'Reverse Entry Awaiting Approval',
            "Reversal of {$itemName} (batch {$batchNumber}, qty {$qtyToReverse}) requires approval.",
            'reverseEntryApproval',
            (string) $reversal->id,
            (int) $approveStock->store_id,
            [$requester->id]
        );

        return $reversal;
    }

    public function approve(StockReversal $reversal, string $comment, ?User $approver = null): bool
    {
        $approver = $approver ?? auth()->user();

        if (!$approver || !$this->canApproveReversal($approver)) {
            return false;
        }

        if ($reversal->status !== StockReversal::STATUS_PENDING) {
            return false;
        }

        DB::transaction(function () use ($reversal, $comment, $approver) {
            $reversal = StockReversal::lockForUpdate()->findOrFail($reversal->id);

            if ($reversal->status !== StockReversal::STATUS_PENDING) {
                throw new InvalidArgumentException('This reversal is no longer pending.');
            }

            $approveStock = ApproveStock::lockForUpdate()
                ->where('id', $reversal->approve_stock_id)
                ->where('batch_number', $reversal->batch_number)
                ->where('store_id', $reversal->store_id)
                ->firstOrFail();

            if ($approveStock->status !== 'approved') {
                throw new InvalidArgumentException('Batch is no longer approved for reversal.');
            }

            $availableQty = (int) $approveStock->qty;
            $qtyToApply = min((int) $reversal->qty_to_reverse, $availableQty);

            if ($qtyToApply <= 0) {
                throw new InvalidArgumentException('No quantity available to reverse on this batch.');
            }

            $approveStock->qty = max(0, $availableQty - $qtyToApply);

            if ($approveStock->qty <= 0 || in_array($reversal->reversal_type, [StockReversal::TYPE_FULL, StockReversal::TYPE_DELETE], true)) {
                $approveStock->qty = 0;
                $approveStock->status = 'reversed';
            }

            $approveStock->save();

            if ($reversal->stock_id) {
                $stock = Stock::lockForUpdate()->find($reversal->stock_id);

                if ($stock) {
                    $stock->qty = max(0, (int) $stock->qty - $qtyToApply);

                    if ($stock->qty <= 0 || $reversal->reversal_type === StockReversal::TYPE_DELETE) {
                        $stock->status = 'reversed';
                    }

                    $stock->updated_by = $approver->id;
                    $stock->save();
                }
            }

            $reversal->update([
                'status'           => StockReversal::STATUS_APPROVED,
                'qty_to_reverse'   => $qtyToApply,
                'approved_by'      => $approver->id,
                'approved_at'      => now(),
                'approval_comment' => trim($comment),
            ]);
        });

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_REVERSE_ENTRY_PENDING_APPROVAL,
            (string) $reversal->id
        );

        return true;
    }

    public function reject(StockReversal $reversal, string $comment, ?User $approver = null): bool
    {
        $approver = $approver ?? auth()->user();

        if (!$approver || !$this->canApproveReversal($approver)) {
            return false;
        }

        if ($reversal->status !== StockReversal::STATUS_PENDING) {
            return false;
        }

        $reversal->update([
            'status'           => StockReversal::STATUS_REJECTED,
            'rejected_by'      => $approver->id,
            'rejected_at'      => now(),
            'approval_comment' => trim($comment),
        ]);

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_REVERSE_ENTRY_PENDING_APPROVAL,
            (string) $reversal->id
        );

        return true;
    }

    protected function resolveQtyToReverse(string $reversalType, ?int $qty, int $availableQty): int
    {
        return match ($reversalType) {
            StockReversal::TYPE_PARTIAL => (int) ($qty ?? 0),
            StockReversal::TYPE_FULL,
            StockReversal::TYPE_DELETE => $availableQty,
            default => throw new InvalidArgumentException('Invalid reversal type.'),
        };
    }
}
