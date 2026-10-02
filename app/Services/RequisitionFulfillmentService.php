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

        if ($requesting->isRequisitionHub()) {
            return false;
        }

        if (!$requesting->routesRequisitionsToHub()) {
            return false;
        }

        return $this->hubStore() !== null;
    }

    /**
     * hub | both | central
     */
    public function routingMode(Store $requesting): string
    {
        $usesHub = $this->shouldRouteToHub($requesting);
        $usesCentral = $requesting->routesRequisitionsToCentral();

        if ($usesHub && $usesCentral) {
            return 'both';
        }

        if ($usesHub) {
            return 'hub';
        }

        return 'central';
    }

    public function shouldRouteToHubOnly(Store $requesting): bool
    {
        return $this->routingMode($requesting) === 'hub';
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
        $mode = $this->routingMode($requesting);
        $hub = $this->hubStore();
        $hubId = $hub ? (int) $hub->id : 0;
        $ownerStoreId = (int) ($item->store_id ?? 0);
        $centralStoreIds = Store::where('store_group', 'central')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $ownerIsCentral = $ownerStoreId && in_array($ownerStoreId, $centralStoreIds, true);
        $ownerIsSatellite = $ownerStoreId && !$ownerIsCentral && $this->storeIsSatellite($ownerStoreId);
        $hubReceipt = ($hub && in_array($mode, ['hub', 'both'], true))
            ? $this->satelliteIssue->findEarliestReceipt((int) $item->id, $hubId)
            : null;

        // Dual-source satellites: send to the hub when it holds the item, even if
        // the catalogue owner is a central store (hub items.store_id is often unset).
        $sendToHub = $mode === 'hub'
            || ($mode === 'both' && $hub && (
                $ownerStoreId === $hubId
                || $ownerIsSatellite
                || $hubReceipt !== null
            ));

        if ($sendToHub) {
            if (!$hub) {
                throw new RuntimeException(
                    'This store is configured to route requisitions to the hub, but no requisition hub store is set in Settings.'
                );
            }

            return [
                'item_store_id' => (int) $hub->id,
                'stock_id'      => $hubReceipt ? (int) ($hubReceipt->stock_id ?? 0) : 0,
                'batch_number'  => $hubReceipt?->batch_number,
                'amount'        => $hubReceipt ? (float) ($hubReceipt->amount ?? 0) : null,
            ];
        }

        $stockQuery = DB::table('approve_stocks')
            ->where('item_id', $item->id)
            ->where('qty', '>', 0)
            ->where('status', 'approved')
            ->whereDate('expiry_date', '>=', now());

        if ($ownerIsCentral) {
            $stockAtOwner = (clone $stockQuery)->where('store_id', $ownerStoreId)
                ->orderBy('expiry_date', 'ASC')
                ->first();

            $stock = $stockAtOwner;

            if (!$stock && !empty($centralStoreIds)) {
                $stock = $stockQuery->whereIn('store_id', $centralStoreIds)
                    ->orderBy('expiry_date', 'ASC')
                    ->first();
            }

            return [
                'item_store_id' => $ownerStoreId,
                'stock_id'      => (int) ($stock->stock_id ?? 0),
                'batch_number'  => $stock->batch_number ?? null,
                'amount'        => isset($stock->amount) ? (float) $stock->amount : null,
            ];
        }

        if (!empty($centralStoreIds)) {
            $stockQuery->whereIn('store_id', $centralStoreIds);
        }

        $stock = $stockQuery
            ->orderBy('expiry_date', 'ASC')
            ->first();

        $itemStoreId = (int) ($stock->store_id ?? ($centralStoreIds[0] ?? $ownerStoreId));

        if (!$itemStoreId) {
            throw new RuntimeException('No central store is configured for this requisition.');
        }

        return [
            'item_store_id' => $itemStoreId,
            'stock_id'      => (int) ($stock->stock_id ?? 0),
            'batch_number'  => $stock->batch_number ?? null,
            'amount'        => isset($stock->amount) ? (float) $stock->amount : null,
        ];
    }

    /**
     * @return array{item_store_id: int, stock_id: int, batch_number: ?string, amount: ?float}
     */
    public function resolveLineMetadataForFulfillmentStore(Store $fulfillment, Item $item): array
    {
        if ($this->usesSatelliteInventory($fulfillment)) {
            $receipt = $this->satelliteIssue->findEarliestReceipt((int) $item->id, (int) $fulfillment->id);

            return [
                'item_store_id' => (int) $fulfillment->id,
                'stock_id'      => $receipt ? (int) ($receipt->stock_id ?? 0) : 0,
                'batch_number'  => $receipt?->batch_number,
                'amount'        => $receipt ? (float) ($receipt->amount ?? 0) : null,
            ];
        }

        $stock = DB::table('approve_stocks')
            ->where('item_id', $item->id)
            ->where('store_id', $fulfillment->id)
            ->where('qty', '>', 0)
            ->where('status', 'approved')
            ->whereDate('expiry_date', '>=', now())
            ->orderBy('expiry_date', 'ASC')
            ->first();

        return [
            'item_store_id' => (int) $fulfillment->id,
            'stock_id'      => (int) ($stock->stock_id ?? 0),
            'batch_number'  => $stock->batch_number ?? null,
            'amount'        => isset($stock->amount) ? (float) $stock->amount : null,
        ];
    }

    /**
     * @return array{show_dropdown: bool, stores: array<int, array{id: int, name: string}>, auto_store_id: int|null}
     */
    public function fulfillmentChoices(Store $requesting, Item $item): array
    {
        $mode = $this->routingMode($requesting);
        $hub = $this->hubStore();

        if ($mode === 'hub') {
            if (! $hub) {
                throw new RuntimeException(
                    'This store is configured to route requisitions to the hub, but no requisition hub store is set in Settings.'
                );
            }

            return [
                'show_dropdown' => false,
                'stores'        => [['id' => (int) $hub->id, 'name' => $hub->name]],
                'auto_store_id' => (int) $hub->id,
            ];
        }

        if ($mode === 'central') {
            $meta = $this->resolveLineMetadata($requesting, $item);
            $autoId = (int) $meta['item_store_id'];
            $store = Store::find($autoId);

            return [
                'show_dropdown' => false,
                'stores'        => $store ? [['id' => $autoId, 'name' => $store->name]] : [],
                'auto_store_id' => $autoId ?: null,
            ];
        }

        $hubHasStock = $hub && $this->satelliteIssue->availableQty((int) $item->id, (int) $hub->id) > 0;
        $centralStores = $this->centralStoresWithStock((int) $item->id);

        $stores = [];
        if ($hubHasStock && $hub) {
            $stores[] = ['id' => (int) $hub->id, 'name' => $hub->name];
        }
        foreach ($centralStores as $store) {
            $stores[] = ['id' => (int) $store->id, 'name' => $store->name];
        }

        $showDropdown = $hubHasStock && $centralStores->isNotEmpty();

        $autoStoreId = null;
        if (! $showDropdown) {
            if ($hubHasStock && $hub) {
                $autoStoreId = (int) $hub->id;
            } elseif ($centralStores->count() === 1) {
                $autoStoreId = (int) $centralStores->first()->id;
            } else {
                $autoStoreId = (int) $this->resolveLineMetadata($requesting, $item)['item_store_id'];
            }
        }

        return [
            'show_dropdown' => $showDropdown,
            'stores'        => $stores,
            'auto_store_id' => $autoStoreId,
        ];
    }

    protected function centralStoresWithStock(int $itemId)
    {
        $centralIds = Store::where('store_group', 'central')
            ->where('status', 'Active')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($centralIds)) {
            return collect();
        }

        $stockedIds = DB::table('approve_stocks')
            ->where('item_id', $itemId)
            ->whereIn('store_id', $centralIds)
            ->where('qty', '>', 0)
            ->where('status', 'approved')
            ->whereDate('expiry_date', '>=', now())
            ->pluck('store_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($stockedIds)) {
            return collect();
        }

        return Store::whereIn('id', $stockedIds)->orderBy('name')->get();
    }

    public function fulfillmentLabelForRequestingStore(Store $requesting): string
    {
        $mode = $this->routingMode($requesting);
        $hubName = $this->hubStore()?->name ?? 'Requisition hub';
        $centralNames = Store::where('store_group', 'central')
            ->where('status', 'Active')
            ->orderBy('name')
            ->pluck('name');
        $centralLabel = $centralNames->isNotEmpty()
            ? $centralNames->join(', ')
            : 'Central Stores';

        if ($mode === 'hub') {
            return $hubName;
        }

        if ($mode === 'both') {
            return $hubName . ' and ' . $centralLabel;
        }

        return $centralLabel;
    }

    protected function storeIsSatellite(int $storeId): bool
    {
        return Store::where('id', $storeId)->where('store_group', 'satellite')->exists();
    }
}
