<?php

namespace App\Services;

use App\Models\SatelliteIssueRequest;
use App\Models\SatelliteStockReceipt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SatelliteIssueService
{
    public function __construct(
        protected StoreContext $storeContext
    ) {}

    public function findEarliestReceipt(int $itemId, int $storeId): ?SatelliteStockReceipt
    {
        return SatelliteStockReceipt::where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->where('qty', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now());
            })
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->first();
    }

    public function availableQty(int $itemId, int $storeId): int
    {
        return (int) SatelliteStockReceipt::where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->where('qty', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now());
            })
            ->sum('qty');
    }

    /**
     * @return array<int, array{receipt: SatelliteStockReceipt, qty: int}>
     */
    public function allocateFromReceipts(Collection $receipts, int $qtyNeeded): array
    {
        $allocations = [];
        $remaining = $qtyNeeded;

        foreach ($receipts as $receipt) {
            if ($remaining <= 0) {
                break;
            }

            $qtyToTake = min($remaining, (int) $receipt->qty);
            if ($qtyToTake <= 0) {
                continue;
            }

            $allocations[] = [
                'receipt' => $receipt,
                'qty'     => $qtyToTake,
            ];

            $remaining -= $qtyToTake;
        }

        return $allocations;
    }

    public function getAvailableReceipts(int $itemId, int $storeId): Collection
    {
        return SatelliteStockReceipt::where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->where('qty', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now());
            })
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->get();
    }

    public function issueRequest(SatelliteIssueRequest $request, ?User $issuer = null): bool
    {
        $issuer = $issuer ?? auth()->user();

        if (!$issuer) {
            return false;
        }

        if (!in_array($request->status, ['submitted', 'partial'], true)) {
            return false;
        }

        $remaining = (int) $request->qty_requested - (int) $request->qty_issued;
        if ($remaining <= 0) {
            return false;
        }

        $receipts = $this->getAvailableReceipts((int) $request->item_id, (int) $request->store_id);
        $allocations = $this->allocateFromReceipts($receipts, $remaining);

        if (array_sum(array_column($allocations, 'qty')) < $remaining) {
            return false;
        }

        DB::transaction(function () use ($request, $issuer, $remaining, $allocations) {
            foreach ($allocations as $allocation) {
                /** @var SatelliteStockReceipt $receipt */
                $receipt = $allocation['receipt'];
                $qtyToTake = $allocation['qty'];

                $receipt->decrement('qty', $qtyToTake);
            }

            $request->update([
                'qty_issued' => (int) $request->qty_issued + $remaining,
                'status'     => 'issued',
                'issued_by'  => $issuer->id,
                'issued_at'  => now(),
            ]);
        });

        return true;
    }

    public function issueBatch(string $issueNo, int $storeId, ?User $issuer = null): int
    {
        $requests = SatelliteIssueRequest::where('issue_no', $issueNo)
            ->where('store_id', $storeId)
            ->whereIn('status', ['submitted', 'partial'])
            ->get();

        $issued = 0;

        foreach ($requests as $request) {
            if ($this->issueRequest($request, $issuer)) {
                $issued++;
            }
        }

        return $issued;
    }

    public function isBatchUsable(?Carbon $expiryDate): bool
    {
        if (!$expiryDate) {
            return true;
        }

        return $expiryDate->startOfDay()->gte(now()->startOfDay());
    }
}
