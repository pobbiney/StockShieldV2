<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockAlertService
{
    public function __construct(
        protected StoreContext $storeContext
    ) {}

    public function getAlerts(): array
    {
        $storeIds = $this->getAlertStoreIds();

        if (empty($storeIds)) {
            return $this->emptyAlerts();
        }

        $stores = Store::whereIn('id', $storeIds)->get(['id', 'store_group', 'name']);

        $centralStoreIds = $stores
            ->where('store_group', 'central')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $satelliteStoreIds = $stores
            ->where('store_group', 'satellite')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $reorderRows = collect();
        $expiringRows = collect();
        $expiredRows = collect();

        if (!empty($centralStoreIds)) {
            $reorderRows = $reorderRows->concat($this->reorderFromApproveStocks($centralStoreIds));
            $expiringRows = $expiringRows->concat($this->expiringFromApproveStocks($centralStoreIds));
            $expiredRows = $expiredRows->concat($this->expiredFromApproveStocks($centralStoreIds));
        }

        if (!empty($satelliteStoreIds)) {
            $reorderRows = $reorderRows->concat($this->reorderFromSatelliteReceipts($satelliteStoreIds));
            $expiringRows = $expiringRows->concat($this->expiringFromSatelliteReceipts($satelliteStoreIds));
            $expiredRows = $expiredRows->concat($this->expiredFromSatelliteReceipts($satelliteStoreIds));
        }

        $reorderItems = $this->mergeReorderRows($reorderRows);
        $expiringItems = $expiringRows->sortBy('expiry_date')->values();
        $expiredItems = $expiredRows->sortBy('expiry_date')->values();

        $reorderItemsCount = $reorderItems->count();
        $expiringCount = $expiringItems->count();
        $expiredCount = $expiredItems->count();
        $expiryAlertCount = $expiringCount + $expiredCount;

        return [
            'reorderItems'       => $reorderItems,
            'reorderItemsCount'  => $reorderItemsCount,
            'expiringItems'      => $expiringItems,
            'expiredItems'       => $expiredItems,
            'expiringCount'      => $expiringCount,
            'expiredCount'       => $expiredCount,
            'expiryAlertCount'   => $expiryAlertCount,
            'notifications'      => $expiringItems,
            'count'              => $expiryAlertCount,
            'stockAlertCount'    => $reorderItemsCount + $expiryAlertCount,
            'alertStoreName'     => $this->resolveAlertStoreName($stores),
        ];
    }

    protected function getAlertStoreIds(): array
    {
        $activeStoreId = $this->storeContext->getActiveStoreId();

        if ($activeStoreId) {
            return [$activeStoreId];
        }

        return $this->storeContext->getScopedStoreIds();
    }

    protected function resolveAlertStoreName(Collection $stores): ?string
    {
        $activeStore = $this->storeContext->getActiveStore();

        if ($activeStore) {
            return $activeStore->name;
        }

        if ($stores->count() === 1) {
            return $stores->first()->name;
        }

        return null;
    }

    protected function emptyAlerts(): array
    {
        return [
            'reorderItems'       => collect(),
            'reorderItemsCount'  => 0,
            'expiringItems'      => collect(),
            'expiredItems'       => collect(),
            'expiringCount'      => 0,
            'expiredCount'       => 0,
            'expiryAlertCount'   => 0,
            'notifications'      => collect(),
            'count'              => 0,
            'stockAlertCount'    => 0,
            'alertStoreName'     => null,
        ];
    }

    protected function reorderFromApproveStocks(array $storeIds): Collection
    {
        return DB::table('approve_stocks')
            ->join('items', 'approve_stocks.item_id', '=', 'items.id')
            ->whereIn('approve_stocks.store_id', $storeIds)
            ->where('approve_stocks.status', 'approved')
            ->where('approve_stocks.qty', '>', 0)
            ->select(
                'items.id',
                'items.name',
                'items.reorder_level',
                DB::raw('SUM(approve_stocks.qty) as total_qty')
            )
            ->groupBy('items.id', 'items.name', 'items.reorder_level')
            ->get();
    }

    protected function reorderFromSatelliteReceipts(array $storeIds): Collection
    {
        return DB::table('satellite_stock_receipts')
            ->join('items', 'satellite_stock_receipts.item_id', '=', 'items.id')
            ->whereIn('satellite_stock_receipts.store_id', $storeIds)
            ->where('satellite_stock_receipts.qty', '>', 0)
            ->select(
                'items.id',
                'items.name',
                'items.reorder_level',
                DB::raw('SUM(satellite_stock_receipts.qty) as total_qty')
            )
            ->groupBy('items.id', 'items.name', 'items.reorder_level')
            ->get();
    }

    protected function mergeReorderRows(Collection $rows): Collection
    {
        return $rows
            ->groupBy('id')
            ->map(function (Collection $group) {
                $first = $group->first();

                return (object) [
                    'id'            => $first->id,
                    'name'          => $first->name,
                    'reorder_level' => $first->reorder_level,
                    'total_qty'     => (int) $group->sum('total_qty'),
                ];
            })
            ->filter(function ($item) {
                $reorderLevel = (int) ($item->reorder_level ?? 0);

                return $reorderLevel > 0 && $item->total_qty <= $reorderLevel;
            })
            ->sortBy('name')
            ->values();
    }

    protected function expiringFromApproveStocks(array $storeIds): Collection
    {
        return DB::table('approve_stocks')
            ->join('items', 'approve_stocks.item_id', '=', 'items.id')
            ->whereIn('approve_stocks.store_id', $storeIds)
            ->where('approve_stocks.status', 'approved')
            ->where('approve_stocks.qty', '>', 0)
            ->whereNotNull('approve_stocks.expiry_date')
            ->whereDate('approve_stocks.expiry_date', '>=', now())
            ->whereDate('approve_stocks.expiry_date', '<=', now()->addMonths(3))
            ->select(
                'items.name',
                'approve_stocks.qty',
                'approve_stocks.expiry_date',
                DB::raw('DATEDIFF(approve_stocks.expiry_date, CURDATE()) as days_left')
            )
            ->orderBy('approve_stocks.expiry_date')
            ->get();
    }

    protected function expiredFromApproveStocks(array $storeIds): Collection
    {
        return DB::table('approve_stocks')
            ->join('items', 'approve_stocks.item_id', '=', 'items.id')
            ->whereIn('approve_stocks.store_id', $storeIds)
            ->where('approve_stocks.status', 'approved')
            ->where('approve_stocks.qty', '>', 0)
            ->whereNotNull('approve_stocks.expiry_date')
            ->whereDate('approve_stocks.expiry_date', '<', now())
            ->select(
                'items.name',
                'approve_stocks.qty',
                'approve_stocks.expiry_date',
                DB::raw('DATEDIFF(approve_stocks.expiry_date, CURDATE()) as days_left')
            )
            ->orderBy('approve_stocks.expiry_date')
            ->get();
    }

    protected function expiringFromSatelliteReceipts(array $storeIds): Collection
    {
        return DB::table('satellite_stock_receipts')
            ->join('items', 'satellite_stock_receipts.item_id', '=', 'items.id')
            ->whereIn('satellite_stock_receipts.store_id', $storeIds)
            ->where('satellite_stock_receipts.qty', '>', 0)
            ->whereNotNull('satellite_stock_receipts.expiry_date')
            ->whereDate('satellite_stock_receipts.expiry_date', '>=', now())
            ->whereDate('satellite_stock_receipts.expiry_date', '<=', now()->addMonths(3))
            ->select(
                'items.name',
                'satellite_stock_receipts.qty',
                'satellite_stock_receipts.expiry_date',
                DB::raw('DATEDIFF(satellite_stock_receipts.expiry_date, CURDATE()) as days_left')
            )
            ->orderBy('satellite_stock_receipts.expiry_date')
            ->get();
    }

    protected function expiredFromSatelliteReceipts(array $storeIds): Collection
    {
        return DB::table('satellite_stock_receipts')
            ->join('items', 'satellite_stock_receipts.item_id', '=', 'items.id')
            ->whereIn('satellite_stock_receipts.store_id', $storeIds)
            ->where('satellite_stock_receipts.qty', '>', 0)
            ->whereNotNull('satellite_stock_receipts.expiry_date')
            ->whereDate('satellite_stock_receipts.expiry_date', '<', now())
            ->select(
                'items.name',
                'satellite_stock_receipts.qty',
                'satellite_stock_receipts.expiry_date',
                DB::raw('DATEDIFF(satellite_stock_receipts.expiry_date, CURDATE()) as days_left')
            )
            ->orderBy('satellite_stock_receipts.expiry_date')
            ->get();
    }
}
