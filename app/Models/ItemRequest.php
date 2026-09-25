<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    protected $fillable = [
        'stock_id',
        'item_id',
        'batch_number',
        'qty',
        'amount',
        'requisition_no',
        'item_store_id',
        'status',
        'created_by',
        'store_id',
        'qty_requested',
        'qty_issued',
        'reason',
        'approved_by',
    ];

    public function itemcode()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function itemname()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function storename()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function sourceStore()
    {
        return $this->belongsTo(Store::class, 'item_store_id', 'id');
    }

    public function staffname()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function authorised()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function issuefrom()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function issues()
    {
        return $this->hasMany(ItemIssue::class, 'item_request_id');
    }

    /**
     * Approved lines this store should issue: assigned here or owned
     * by this store in the item catalogue. Other stores' lines on the
     * same requisition number are excluded.
     */
    public function scopeApprovedForIssuingStores($query, array $storeIds, bool $hasGlobalAccess = false)
    {
        $query->where('status', 'request approved');

        if ($hasGlobalAccess || empty($storeIds)) {
            return $query;
        }

        $storeIds = array_values(array_unique(array_map('intval', $storeIds)));

        return $query->where(function ($match) use ($storeIds) {
            $match->whereIn('item_store_id', $storeIds)
                ->orWhereHas('itemname', function ($item) use ($storeIds) {
                    $item->whereIn('store_id', $storeIds);
                });
        });
    }

    public function approvedQuantity(): ?int
    {
        return $this->qty !== null ? (int) $this->qty : null;
    }

    public function fulfillmentTarget(): int
    {
        return $this->approvedQuantity() ?? (int) $this->qty_requested;
    }

    public function itemTotalQtyMultiplier(): ?int
    {
        $item = $this->relationLoaded('itemname') ? $this->itemname : $this->itemname()->first();

        if (!$item) {
            return null;
        }

        $multiplier = $item->total_qty ?? null;

        if ($multiplier === null || $multiplier === '') {
            return null;
        }

        $multiplier = (int) $multiplier;

        return $multiplier > 0 ? $multiplier : null;
    }

    public function effectiveQuantity(int $baseQty): int
    {
        $multiplier = $this->itemTotalQtyMultiplier();

        if ($multiplier === null) {
            return $baseQty;
        }

        return $baseQty * $multiplier;
    }

    public function effectiveRequestedQuantity(): int
    {
        return $this->effectiveQuantity((int) $this->qty_requested);
    }

    public function effectiveIssuedQuantity(): int
    {
        return $this->effectiveQuantity($this->issuedQuantity());
    }

    public function issuedQuantity(): int
    {
        $statuses = ['issued', 'received'];

        if ($this->relationLoaded('issues')) {
            $linked = (int) $this->issues->whereIn('status', $statuses)->sum('qty');
            if ($linked > 0 || $this->issues->whereIn('status', $statuses)->isNotEmpty()) {
                return $linked;
            }
        } elseif ($this->issues()->whereIn('status', $statuses)->exists()) {
            return (int) $this->issues()->whereIn('status', $statuses)->sum('qty');
        }

        if (!$this->requisition_no) {
            return 0;
        }

        $centralQty = (int) ItemIssue::where('requisition_no', $this->requisition_no)
            ->where('item_id', $this->item_id)
            ->where('issue_to', $this->store_id)
            ->whereIn('status', $statuses)
            ->sum('qty');

        $satelliteQty = (int) SatelliteItemIssue::where('requisition_no', $this->requisition_no)
            ->where('item_id', $this->item_id)
            ->where('issue_to', $this->store_id)
            ->whereIn('status', $statuses)
            ->sum('qty');

        return $centralQty + $satelliteQty;
    }

    public function receivedQuantity(): int
    {
        if ($this->relationLoaded('issues')) {
            return (int) $this->issues->where('status', 'received')->sum('qty');
        }

        if ($this->issues()->where('status', 'received')->exists()) {
            return (int) $this->issues()->where('status', 'received')->sum('qty');
        }

        if (!$this->requisition_no) {
            return 0;
        }

        $centralQty = (int) ItemIssue::where('requisition_no', $this->requisition_no)
            ->where('item_id', $this->item_id)
            ->where('issue_to', $this->store_id)
            ->where('status', 'received')
            ->sum('qty');

        $satelliteQty = (int) SatelliteItemIssue::where('requisition_no', $this->requisition_no)
            ->where('item_id', $this->item_id)
            ->where('issue_to', $this->store_id)
            ->where('status', 'received')
            ->sum('qty');

        return $centralQty + $satelliteQty;
    }

    public function issuedAt(): ?\Illuminate\Support\Carbon
    {
        $statuses = ['issued', 'received'];

        if ($this->relationLoaded('issues')) {
            $issued = $this->issues->whereIn('status', $statuses);
            if ($issued->isNotEmpty()) {
                return $issued->max('updated_at');
            }
        } else {
            $latest = $this->issues()->whereIn('status', $statuses)->max('updated_at');
            if ($latest) {
                return \Illuminate\Support\Carbon::parse($latest);
            }
        }

        if (!$this->requisition_no) {
            return null;
        }

        $legacy = ItemIssue::where('requisition_no', $this->requisition_no)
            ->where('item_id', $this->item_id)
            ->where('issue_to', $this->store_id)
            ->whereIn('status', $statuses)
            ->orderByDesc('updated_at')
            ->value('updated_at');

        return $legacy ? \Illuminate\Support\Carbon::parse($legacy) : null;
    }

    public function receivedAt(): ?\Illuminate\Support\Carbon
    {
        if ($this->relationLoaded('issues')) {
            $received = $this->issues->where('status', 'received');
            if ($received->isNotEmpty()) {
                $at = $received->max('received_at') ?? $received->max('updated_at');
                return $at ? \Illuminate\Support\Carbon::parse($at) : null;
            }
        } else {
            $latest = $this->issues()->where('status', 'received')->max('received_at')
                ?? $this->issues()->where('status', 'received')->max('updated_at');
            if ($latest) {
                return \Illuminate\Support\Carbon::parse($latest);
            }
        }

        return null;
    }

    public function statusLabel(): string
    {
        return $this->fulfillmentLabel();
    }

    public function statusClass(): string
    {
        return match ($this->fulfillmentStatus()) {
            'fulfilled' => 'fulfilled',
            'received'  => 'received',
            'partial'   => 'partial',
            'rejected'  => 'rejected',
            'draft'     => 'draft',
            default     => match ($this->status) {
                'request approved' => 'approved',
                'pending issue'     => 'pending-issue',
                'issued'            => 'issued',
                'received'          => 'received',
                default            => 'submitted',
            },
        };
    }

    public function fulfillmentStatus(): string
    {
        if ($this->status === 'rejected') {
            return 'rejected';
        }

        if ($this->status === 'pending') {
            return 'draft';
        }

        if ($this->status === 'received') {
            return 'received';
        }

        $received = $this->receivedQuantity();
        $target = $this->fulfillmentTarget();

        if ($target > 0 && $received >= $target) {
            return 'fulfilled';
        }

        $issued = $this->issuedQuantity();
        $target = $this->fulfillmentTarget();

        if ($target > 0 && $issued >= $target) {
            return 'fulfilled';
        }

        if ($issued > 0) {
            return 'partial';
        }

        return 'pending';
    }

    public function fulfillmentLabel(): string
    {
        return match ($this->fulfillmentStatus()) {
            'draft'      => 'Draft',
            'rejected'   => 'Rejected',
            'fulfilled'  => 'Fulfilled',
            'received'   => 'Received into Store',
            'partial'    => 'Partially Issued',
            default      => match ($this->status) {
                'pending request'   => 'Awaiting Approval',
                'request approved'  => 'Approved — Awaiting Issue',
                'pending issue'     => 'Pending Issue Approval',
                'issued'            => 'Issued — Pending Pickup',
                'received'          => 'Received into Store',
                default             => ucfirst($this->status ?? 'Pending'),
            },
        };
    }
}
