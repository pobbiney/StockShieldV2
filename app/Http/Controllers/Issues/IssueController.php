<?php

namespace App\Http\Controllers\Issues;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\SatelliteItemIssue;
use App\Models\SatelliteStockReceipt;
use App\Services\NotificationService;
use App\Services\RequisitionFulfillmentService;
use App\Services\SatelliteIssueService;
use App\Services\StoreContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IssueController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected NotificationService $notifications,
        protected RequisitionFulfillmentService $requisitionFulfillment,
        protected SatelliteIssueService $satelliteIssue
    ) {}

    public function getviewStoreRequest($requisition_no)
    {
        $storeIds = $this->storeContext->getScopedStoreIds();
        $decodeID = Crypt::decrypt($requisition_no);

        $listrequest = ItemRequest::with(['itemcode', 'itemname.unitname', 'storename', 'staffname'])
            ->whereIn('item_store_id', $storeIds)
            ->where('requisition_no', $decodeID)
            ->where('status', 'request approved')
            ->orderByDesc('id')
            ->get();

        $stockAvailability = [];
        foreach ($listrequest as $line) {
            $stockAvailability[$line->id] = $this->buildAvailabilitySummary($line);
        }

        return view('stock.viewStoreRequest', [
            'listrequest'       => $listrequest,
            'requisitionNo'     => $decodeID,
            'stockAvailability' => $stockAvailability,
        ]);
    }

    public function addIssueRequest(Request $request)
    {
        $requestIds = $request->input('request_id', []);
        $qtyInputs  = $request->input('qty', []);

        if (empty($requestIds)) {
            return back()->with('message_error', 'No items selected to issue.');
        }

        $itemRequests = ItemRequest::with('itemname')
            ->whereIn('id', $requestIds)
            ->where('status', 'request approved')
            ->get()
            ->keyBy('id');

        $errors = [];
        $issuePlan = [];
        $noStockLines = [];
        $expiryWarnings = [];

        foreach ($requestIds as $itemRequestId) {
            $itemRequest = $itemRequests->get($itemRequestId);

            if (!$itemRequest) {
                $errors[] = "Line item #{$itemRequestId} was not found or is no longer approved.";
                continue;
            }

            $itemName = $itemRequest->itemname->name ?? 'Item';
            $rawQty = $qtyInputs[$itemRequestId] ?? null;
            $availability = $this->buildAvailabilitySummary($itemRequest);

            if ($rawQty === null || $rawQty === '') {
                if ($availability['available_qty'] <= 0 || $availability['expired_only']) {
                    $noStockLines[] = $itemRequest;
                }
                continue;
            }

            if (!is_numeric($rawQty)) {
                $errors[] = "{$itemName}: quantity must be a valid number.";
                continue;
            }

            $qtyToIssue = (int) $rawQty;

            if ($qtyToIssue < 0) {
                $errors[] = "{$itemName}: quantity cannot be negative.";
                continue;
            }

            if ($qtyToIssue === 0) {
                if ($availability['available_qty'] <= 0 || $availability['expired_only']) {
                    $noStockLines[] = $itemRequest;
                }
                continue;
            }

            $approvedQty = (int) ($itemRequest->qty ?? $itemRequest->qty_requested);
            if ($qtyToIssue > $approvedQty) {
                $errors[] = "{$itemName}: quantity to issue ({$qtyToIssue}) exceeds approved quantity ({$approvedQty}).";
                continue;
            }

            if ($availability['expired_only']) {
                $errors[] = "{$itemName}: all stock batches have expired. Cannot issue expired items.";
                continue;
            }

            if ($availability['available_qty'] <= 0) {
                $errors[] = "{$itemName}: no non-expired stock available at this store.";
                continue;
            }

            if ($qtyToIssue > $availability['available_qty']) {
                $errors[] = "{$itemName}: requested quantity ({$qtyToIssue}) exceeds available stock ({$availability['available_qty']}).";
                continue;
            }

            $useSatelliteInventory = $this->requisitionFulfillment->usesSatelliteInventoryByStoreId(
                (int) $itemRequest->item_store_id
            );

            if ($useSatelliteInventory) {
                $receipts = $this->satelliteIssue->getAvailableReceipts(
                    (int) $itemRequest->item_id,
                    (int) $itemRequest->item_store_id
                );
                $receiptAllocations = $this->satelliteIssue->allocateFromReceipts($receipts, $qtyToIssue);
                $allocations = array_map(fn ($row) => [
                    'receipt' => $row['receipt'],
                    'qty'     => $row['qty'],
                ], $receiptAllocations);
            } else {
                $batches = $this->getAvailableBatches($itemRequest);
                $allocations = $this->allocateFromBatches($batches, $qtyToIssue);
            }

            if ($this->allocatedTotal($allocations) < $qtyToIssue) {
                $errors[] = "{$itemName}: unable to allocate the full quantity from available batches.";
                continue;
            }

            foreach ($allocations as $allocation) {
                $expiryDate = isset($allocation['receipt'])
                    ? $allocation['receipt']->expiry_date
                    : $allocation['stock']->expiry_date;

                if (!$expiryDate) {
                    continue;
                }

                $batchNumber = isset($allocation['receipt'])
                    ? $allocation['receipt']->batch_number
                    : $allocation['stock']->batch_number;

                $daysToExpiry = now()->startOfDay()->diffInDays(
                    Carbon::parse($expiryDate)->startOfDay(),
                    false
                );
                if ($daysToExpiry <= 30) {
                    $expiryWarnings[] = "{$itemName} (batch {$batchNumber}): expires in {$daysToExpiry} day(s).";
                }
            }

            $issuePlan[] = [
                'item_request'            => $itemRequest,
                'qty_to_issue'            => $qtyToIssue,
                'allocations'             => $allocations,
                'use_satellite_inventory' => $useSatelliteInventory,
            ];
        }

        if (!empty($errors)) {
            return back()
                ->withInput()
                ->with('message_error', implode(' ', $errors));
        }

        if (empty($issuePlan) && empty($noStockLines)) {
            return back()->with('message_error', 'Enter a quantity greater than zero for at least one item with available stock.');
        }

        $noStockLines = collect($noStockLines)->unique('id')->values()->all();

        try {
            DB::transaction(function () use ($issuePlan, $noStockLines) {
                foreach ($issuePlan as $plan) {
                    /** @var ItemRequest $itemRequest */
                    $itemRequest = $plan['item_request'];
                    $useSatelliteInventory = (bool) ($plan['use_satellite_inventory'] ?? false);

                    foreach ($plan['allocations'] as $allocation) {
                        $qtyToTake = $allocation['qty'];

                        if ($useSatelliteInventory && isset($allocation['receipt'])) {
                            /** @var SatelliteStockReceipt $receipt */
                            $receipt = $allocation['receipt'];

                            SatelliteItemIssue::create([
                                'satellite_stock_receipt_id' => $receipt->id,
                                'stock_id'                   => $receipt->stock_id,
                                'item_id'                    => $receipt->item_id,
                                'batch_number'               => $receipt->batch_number,
                                'qty_requested'              => $itemRequest->qty ?? $itemRequest->qty_requested,
                                'qty'                        => $qtyToTake,
                                'amount'                     => $receipt->amount,
                                'requisition_no'             => $itemRequest->requisition_no,
                                'item_request_id'            => $itemRequest->id,
                                'issue_to'                   => $itemRequest->store_id,
                                'store_id'                   => $itemRequest->item_store_id,
                                'created_by'                 => Auth::id(),
                                'status'                     => 'pending',
                                'status_two'                 => 'pending',
                            ]);

                            continue;
                        }

                        $stock = $allocation['stock'];

                        ItemIssue::create([
                            'stock_id'        => $stock->stock_id,
                            'item_id'         => $stock->item_id,
                            'batch_number'    => $stock->batch_number,
                            'qty_requested'   => $itemRequest->qty ?? $itemRequest->qty_requested,
                            'qty'             => $qtyToTake,
                            'amount'          => $stock->amount,
                            'requisition_no'  => $itemRequest->requisition_no,
                            'item_request_id' => $itemRequest->id,
                            'issue_to'        => $itemRequest->store_id,
                            'store_id'        => $itemRequest->item_store_id,
                            'created_by'      => Auth::id(),
                            'status'          => 'pending',
                            'status_two'      => 'pending',
                        ]);
                    }

                    $itemRequest->update(['status' => 'pending issue']);
                }

                foreach ($noStockLines as $itemRequest) {
                    $itemRequest->update([
                        'status'     => 'pending issue',
                        'qty_issued' => 0,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('message_error', 'Something went wrong while issuing items: ' . $e->getMessage());
        }

        $issuedCount = count($issuePlan);
        $noStockCount = count($noStockLines);
        $messageParts = [];

        if ($issuedCount > 0) {
            $messageParts[] = "{$issuedCount} item(s) submitted for issue and sent for HOD approval.";
        }

        if ($noStockCount > 0) {
            $messageParts[] = "{$noStockCount} item(s) with no available stock recorded as zero and sent for HOD review.";
        }

        $message = implode(' ', $messageParts);
        if (!empty($expiryWarnings)) {
            $message .= ' Note: ' . implode(' ', $expiryWarnings);
        }

        $requisitionNos = collect($issuePlan)
            ->map(fn ($plan) => $plan['item_request']->requisition_no ?? null)
            ->merge(collect($noStockLines)->pluck('requisition_no'))
            ->filter()
            ->unique();

        foreach ($requisitionNos as $requisitionNo) {
            $this->notifications->markResolvedByTypeAndReference(
                NotificationService::TYPE_REQUISITION_APPROVED,
                $requisitionNo
            );
        }

        foreach ($requisitionNos as $requisitionNo) {
            $this->notifications->notifyUsersForAction(
                NotificationService::TYPE_ISSUE_PENDING_APPROVAL,
                'Issue Pending Approval',
                "Issued items for {$requisitionNo} are awaiting HOD approval.",
                'IssueApproval',
                $requisitionNo,
                null,
                [Auth::id()]
            );
        }

        return back()->with('message_success', $message);
    }

    private function getAvailableBatches(ItemRequest $itemRequest): Collection
    {
        return ApproveStock::where('item_id', $itemRequest->item_id)
            ->where('store_id', $itemRequest->item_store_id)
            ->where('status', 'approved')
            ->where('qty', '>', 0)
            ->whereDate('expiry_date', '>=', now())
            ->orderBy('expiry_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
    }

    private function itemTotalQtyMultiplier(ItemRequest $itemRequest): ?int
    {
        $multiplier = $itemRequest->itemname->total_qty ?? null;

        if ($multiplier === null || $multiplier === '') {
            return null;
        }

        $multiplier = (int) $multiplier;

        return $multiplier > 0 ? $multiplier : null;
    }

    private function effectiveQtyFromStockQty(int $stockQty, ?int $totalQtyMultiplier): int
    {
        if ($totalQtyMultiplier === null) {
            return $stockQty;
        }

        return $stockQty * $totalQtyMultiplier;
    }

    private function buildAvailabilitySummary(ItemRequest $itemRequest): array
    {
        $totalQtyMultiplier = $this->itemTotalQtyMultiplier($itemRequest);

        if ($this->requisitionFulfillment->usesSatelliteInventoryByStoreId((int) $itemRequest->item_store_id)) {
            $allReceipts = SatelliteStockReceipt::where('item_id', $itemRequest->item_id)
                ->where('store_id', $itemRequest->item_store_id)
                ->where('qty', '>', 0)
                ->orderBy('expiry_date', 'ASC')
                ->get();

            $validReceipts = $allReceipts->filter(fn ($receipt) => $this->isReceiptUsable($receipt));
            $expiredReceipts = $allReceipts->filter(fn ($receipt) => !$this->isReceiptUsable($receipt));

            $rawAvailableQty = (int) $validReceipts->sum('qty');
            $availableEffectiveQty = $this->effectiveQtyFromStockQty($rawAvailableQty, $totalQtyMultiplier);

            return [
                'available_qty'           => $rawAvailableQty,
                'available_effective_qty' => $availableEffectiveQty,
                'total_qty_multiplier'    => $totalQtyMultiplier,
                'batch_count'             => $validReceipts->count(),
                'expired_only'            => $allReceipts->isNotEmpty() && $validReceipts->isEmpty(),
                'has_expired'             => $expiredReceipts->isNotEmpty(),
                'nearest_expiry'          => $validReceipts->first()?->expiry_date,
                'batches'                 => $validReceipts->map(function ($receipt) use ($totalQtyMultiplier) {
                    $stockQty = (int) $receipt->qty;

                    return [
                        'batch_number'  => $receipt->batch_number,
                        'qty'           => $stockQty,
                        'effective_qty' => $this->effectiveQtyFromStockQty($stockQty, $totalQtyMultiplier),
                        'expiry_date'   => $receipt->expiry_date ? Carbon::parse($receipt->expiry_date)->format('Y-m-d') : null,
                    ];
                })->values()->all(),
            ];
        }

        $allBatches = ApproveStock::where('item_id', $itemRequest->item_id)
            ->where('store_id', $itemRequest->item_store_id)
            ->where('status', 'approved')
            ->where('qty', '>', 0)
            ->orderBy('expiry_date', 'ASC')
            ->get();

        $validBatches = $allBatches->filter(fn ($batch) => $this->isBatchUsable($batch->expiry_date));

        $expiredBatches = $allBatches->filter(fn ($batch) => !$this->isBatchUsable($batch->expiry_date));

        $rawAvailableQty = (int) $validBatches->sum('qty');
        $availableEffectiveQty = $this->effectiveQtyFromStockQty($rawAvailableQty, $totalQtyMultiplier);

        return [
            'available_qty'           => $rawAvailableQty,
            'available_effective_qty' => $availableEffectiveQty,
            'total_qty_multiplier'    => $totalQtyMultiplier,
            'batch_count'             => $validBatches->count(),
            'expired_only'            => $allBatches->isNotEmpty() && $validBatches->isEmpty(),
            'has_expired'             => $expiredBatches->isNotEmpty(),
            'nearest_expiry'          => $validBatches->first()?->expiry_date,
            'batches'                 => $validBatches->map(function ($b) use ($totalQtyMultiplier) {
                $stockQty = (int) $b->qty;

                return [
                    'batch_number'  => $b->batch_number,
                    'qty'           => $stockQty,
                    'effective_qty' => $this->effectiveQtyFromStockQty($stockQty, $totalQtyMultiplier),
                    'expiry_date'   => $b->expiry_date ? Carbon::parse($b->expiry_date)->format('Y-m-d') : null,
                ];
            })->values()->all(),
        ];
    }

    private function isReceiptUsable(SatelliteStockReceipt $receipt): bool
    {
        if (!$receipt->expiry_date) {
            return true;
        }

        return Carbon::parse($receipt->expiry_date)->startOfDay()->gte(now()->startOfDay());
    }

    private function isBatchUsable($expiryDate): bool
    {
        if (!$expiryDate) {
            return false;
        }

        return Carbon::parse($expiryDate)->startOfDay()->gte(now()->startOfDay());
    }

    /**
     * FEFO: allocate quantity from earliest-expiring batches first.
     *
     * @return array<int, array{stock: ApproveStock, qty: int}>
     */
    private function allocateFromBatches(Collection $batches, int $qtyNeeded): array
    {
        $allocations = [];
        $remaining = $qtyNeeded;

        foreach ($batches as $stock) {
            if ($remaining <= 0) {
                break;
            }

            $qtyToTake = min($remaining, (int) $stock->qty);
            if ($qtyToTake <= 0) {
                continue;
            }

            $allocations[] = [
                'stock' => $stock,
                'qty'   => $qtyToTake,
            ];

            $remaining -= $qtyToTake;
        }

        return $allocations;
    }

    private function allocatedTotal(array $allocations): int
    {
        return array_sum(array_column($allocations, 'qty'));
    }
}
