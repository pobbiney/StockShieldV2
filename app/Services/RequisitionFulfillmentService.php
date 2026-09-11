<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RequisitionFulfillmentService
{
    public function __construct(
        protected SatelliteIssueService $satelliteIssue
    ) {}

    public function hubStore(): ?Store
    {
        return Store::requisitionHub();
    }

    public function shouldRouteToHub(Store $requesting): bool
    {
        if ($requesting->store_group !== 'satellite') {
            return false;
        }

        if (!$requesting->routesRequisitionsToHub()) {
            return false;
        }

        return $this->hubStore() !== null;
    }

    public function usesSatelliteInventory(?Store $fulfillmentStore): bool
    {
        return $fulfillmentStore && $fulfillmentStore->store_group === 'satellite';
    }

    public function usesSatelliteInventoryByStoreId(int $storeId): bool
    {
        $store = Store::find($storeId);

        return $this->usesSatelliteInventory($store);
    }

    /**
     * @return array{item_store_id: int, stock_id: int, batch_number: ?string, amount: ?float}
     */
    public function resolveLineMetadata(Store $requesting, Item $item): array
    {
        if ($requesting->routesRequisitionsToHub() && $requesting->store_group === 'satellite') {
            $hub = $this->hubStore();

            if (!$hub) {
                throw new RuntimeException(
                    'This store is configured to route requisitions to the hub, but no requisition hub store is set in Settings.'
                );
            }

            $receipt = $this->satelliteIssue->findEarliestReceipt((int) $item->id, (int) $hub->id);

            return [
                'item_store_id' => (int) $hub->id,
                'stock_id'      => $receipt ? (int) ($receipt->stock_id ?? 0) : 0,
                'batch_number'  => $receipt?->batch_number,
                'amount'        => $receipt ? (float) ($receipt->amount ?? 0) : null,
            ];
        }

        $centralStoreIds = Store::where('store_group', 'central')->pluck('id')->map(fn ($id) => (int) $id)->all();

        $stockQuery = DB::table('approve_stocks')
            ->where('item_id', $item->id)
            ->where('qty', '>', 0)
            ->where('status', 'approved')
            ->whereDate('expiry_date', '>=', now());

        if (!empty($centralStoreIds)) {
            $stockQuery->whereIn('store_id', $centralStoreIds);
        }

        $stock = $stockQuery
            ->orderBy('expiry_date', 'ASC')
            ->first();

        $itemStoreId = $stock->store_id ?? ($centralStoreIds[0] ?? (int) $item->store_id);

        if (!$itemStoreId) {
            throw new RuntimeException('No central store is configured for this requisition.');
        }

        return [
            'item_store_id' => (int) $itemStoreId,
            'stock_id'      => (int) ($stock->stock_id ?? 0),
            'batch_number'  => $stock->batch_number ?? null,
            'amount'        => isset($stock->amount) ? (float) $stock->amount : null,
        ];
    }

    public function fulfillmentLabelForRequestingStore(Store $requesting): string
    {
        if ($this->shouldRouteToHub($requesting)) {
            return $this->hubStore()->name ?? 'Requisition hub';
        }

        $centralNames = Store::where('store_group', 'central')
            ->where('status', 'Active')
            ->orderBy('name')
            ->pluck('name');

        return $centralNames->isNotEmpty()
            ? $centralNames->join(', ')
            : 'Central Stores';
    }
}
