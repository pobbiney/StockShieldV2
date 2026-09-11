<?php

namespace App\Services;

use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\SatelliteStockEntry;
use App\Models\Stock;
use App\Models\StockReversal;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RejectedItemsService
{
    public const TYPE_REQUISITION = 'requisition';
    public const TYPE_STOCK_ENTRY = 'stock_entry';
    public const TYPE_SATELLITE_ENTRY = 'satellite_entry';
    public const TYPE_ISSUE = 'issue';
    public const TYPE_REVERSE_ENTRY = 'reverse_entry';

    public function collect(array $storeIds, bool $globalAccess, ?string $typeFilter = null): array
    {
        $items = collect()
            ->merge($this->collectRequisitions($storeIds, $globalAccess))
            ->merge($this->collectStockEntries($storeIds, $globalAccess))
            ->merge($this->collectSatelliteEntries($storeIds, $globalAccess))
            ->merge($this->collectIssues($storeIds, $globalAccess))
            ->merge($this->collectReverseEntries($storeIds, $globalAccess));

        $typeCounts = [
            'all'            => $items->count(),
            'requisition'    => $items->where('type', self::TYPE_REQUISITION)->count(),
            'stock'          => $items->whereIn('type', [self::TYPE_STOCK_ENTRY, self::TYPE_SATELLITE_ENTRY])->count(),
            'issue'          => $items->where('type', self::TYPE_ISSUE)->count(),
            'reverse_entry'  => $items->where('type', self::TYPE_REVERSE_ENTRY)->count(),
        ];

        if ($typeFilter && $typeFilter !== 'all') {
            $items = $this->applyTypeFilter($items, $typeFilter);
        }

        $items = $items
            ->sortByDesc(fn (array $row) => $row['rejected_at']?->timestamp ?? 0)
            ->values();

        return [
            'items'      => $items,
            'typeCounts' => $typeCounts,
        ];
    }

    protected function applyTypeFilter(Collection $items, string $typeFilter): Collection
    {
        return match ($typeFilter) {
            'requisition'   => $items->where('type', self::TYPE_REQUISITION),
            'stock'         => $items->whereIn('type', [self::TYPE_STOCK_ENTRY, self::TYPE_SATELLITE_ENTRY]),
            'issue'         => $items->where('type', self::TYPE_ISSUE),
            'reverse_entry' => $items->where('type', self::TYPE_REVERSE_ENTRY),
            default         => $items,
        };
    }

    protected function collectRequisitions(array $storeIds, bool $globalAccess): Collection
    {
        return ItemRequest::with(['itemcode', 'itemname', 'storename', 'approvedByUser'])
            ->where('status', 'rejected')
            ->whereNotNull('requisition_no')
            ->when(
                !$globalAccess,
                fn ($query) => $query->whereIn('store_id', $storeIds)
            )
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (ItemRequest $row) => $this->normalizeRow(
                type: self::TYPE_REQUISITION,
                typeLabel: 'Requisition',
                reference: $row->requisition_no ?? '—',
                itemName: $row->itemname->name ?? '—',
                itemCode: $row->itemcode->item_code ?? '—',
                storeLabel: $row->storename->name ?? '—',
                qty: (int) ($row->qty_requested ?? 0),
                reason: $row->reason ?? '—',
                rejectedBy: $row->approvedByUser->name ?? '—',
                rejectedAt: $row->updated_at,
            ));
    }

    protected function collectStockEntries(array $storeIds, bool $globalAccess): Collection
    {
        return Stock::with(['itemcode', 'itemname', 'storename', 'rejectedByUser'])
            ->where('status', 'rejected')
            ->when(
                !$globalAccess,
                fn ($query) => $query->whereIn('store_id', $storeIds)
            )
            ->orderByDesc('rejected_at')
            ->get()
            ->map(fn (Stock $row) => $this->normalizeRow(
                type: self::TYPE_STOCK_ENTRY,
                typeLabel: 'Stock Entry',
                reference: $row->batch_number ?? '—',
                itemName: $row->itemname->name ?? '—',
                itemCode: $row->itemcode->item_code ?? '—',
                storeLabel: $row->storename->name ?? '—',
                qty: (int) ($row->qty ?? 0),
                reason: $row->rejection_reason ?? '—',
                rejectedBy: $row->rejectedByUser->name ?? '—',
                rejectedAt: $row->rejected_at ?? $row->updated_at,
            ));
    }

    protected function collectSatelliteEntries(array $storeIds, bool $globalAccess): Collection
    {
        return SatelliteStockEntry::with(['itemcode', 'itemname', 'storename', 'rejectedByUser'])
            ->where('status', 'rejected')
            ->when(
                !$globalAccess,
                fn ($query) => $query->whereIn('store_id', $storeIds)
            )
            ->orderByDesc('rejected_at')
            ->get()
            ->map(fn (SatelliteStockEntry $row) => $this->normalizeRow(
                type: self::TYPE_SATELLITE_ENTRY,
                typeLabel: 'Satellite Entry',
                reference: $row->batch_number ?? '—',
                itemName: $row->itemname->name ?? '—',
                itemCode: $row->itemcode->item_code ?? '—',
                storeLabel: $row->storename->name ?? '—',
                qty: (int) ($row->qty ?? 0),
                reason: $row->rejection_reason ?? '—',
                rejectedBy: $row->rejectedByUser->name ?? '—',
                rejectedAt: $row->rejected_at ?? $row->updated_at,
            ));
    }

    protected function collectIssues(array $storeIds, bool $globalAccess): Collection
    {
        return ItemIssue::with(['itemcode', 'itemname', 'storename', 'issuefrom', 'rejectedByUser'])
            ->where('status', 'rejected')
            ->when(
                !$globalAccess,
                fn ($query) => $query->where(function ($inner) use ($storeIds) {
                    $inner->whereIn('store_id', $storeIds)
                        ->orWhereIn('issue_to', $storeIds);
                })
            )
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (ItemIssue $row) {
                $from = $row->issuefrom->name ?? '—';
                $to = $row->storename->name ?? '—';

                return $this->normalizeRow(
                    type: self::TYPE_ISSUE,
                    typeLabel: 'Issue Approval',
                    reference: $row->requisition_no ?? ($row->batch_number ?? '—'),
                    itemName: $row->itemname->name ?? '—',
                    itemCode: $row->itemcode->item_code ?? '—',
                    storeLabel: "{$from} → {$to}",
                    qty: (int) ($row->qty ?? 0),
                    reason: $row->reason ?? '—',
                    rejectedBy: $row->rejectedByUser->name ?? '—',
                    rejectedAt: $row->updated_at,
                );
            });
    }

    protected function collectReverseEntries(array $storeIds, bool $globalAccess): Collection
    {
        return StockReversal::with(['itemcode', 'itemname', 'store', 'rejectedByUser'])
            ->where('status', StockReversal::STATUS_REJECTED)
            ->when(
                !$globalAccess,
                fn ($query) => $query->whereIn('store_id', $storeIds)
            )
            ->orderByDesc('rejected_at')
            ->get()
            ->map(fn (StockReversal $row) => $this->normalizeRow(
                type: self::TYPE_REVERSE_ENTRY,
                typeLabel: 'Reverse Entry',
                reference: $row->batch_number ?? '—',
                itemName: $row->itemname->name ?? '—',
                itemCode: $row->itemcode->item_code ?? '—',
                storeLabel: $row->store->name ?? '—',
                qty: (int) ($row->qty_to_reverse ?? 0),
                reason: $row->approval_comment ?? ($row->reason ?? '—'),
                rejectedBy: $row->rejectedByUser->name ?? '—',
                rejectedAt: $row->rejected_at ?? $row->updated_at,
            ));
    }

    protected function normalizeRow(
        string $type,
        string $typeLabel,
        string $reference,
        string $itemName,
        string $itemCode,
        string $storeLabel,
        ?int $qty,
        string $reason,
        string $rejectedBy,
        mixed $rejectedAt,
    ): array {
        $date = $rejectedAt instanceof Carbon
            ? $rejectedAt
            : ($rejectedAt ? Carbon::parse($rejectedAt) : null);

        return [
            'type'         => $type,
            'type_label'   => $typeLabel,
            'reference'    => $reference,
            'item_name'    => $itemName,
            'item_code'    => $itemCode,
            'store_label'  => $storeLabel,
            'qty'          => $qty,
            'reason'       => $reason !== '' ? $reason : '—',
            'rejected_by'  => $rejectedBy,
            'rejected_at'  => $date,
        ];
    }
}
