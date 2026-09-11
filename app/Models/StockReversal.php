<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReversal extends Model
{
    public const TYPE_PARTIAL = 'partial';
    public const TYPE_FULL = 'full';
    public const TYPE_DELETE = 'delete';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'item_id',
        'batch_number',
        'stock_id',
        'approve_stock_id',
        'store_id',
        'reversal_type',
        'qty_to_reverse',
        'reason',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'approval_comment',
    ];

    protected $casts = [
        'qty_to_reverse' => 'integer',
        'approved_at'    => 'datetime',
        'rejected_at'    => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function itemcode()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function itemname()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function approveStock()
    {
        return $this->belongsTo(ApproveStock::class, 'approve_stock_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function typeLabel(): string
    {
        return match ($this->reversal_type) {
            self::TYPE_PARTIAL => 'Partial',
            self::TYPE_FULL    => 'Full',
            self::TYPE_DELETE  => 'Delete (void)',
            default            => ucfirst($this->reversal_type ?? '—'),
        };
    }
}
