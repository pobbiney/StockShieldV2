<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatelliteItemIssue extends Model
{
    protected $fillable = [
        'satellite_stock_receipt_id',
        'stock_id',
        'item_id',
        'unit_id',
        'batch_number',
        'qty',
        'qty_requested',
        'amount',
        'requisition_no',
        'item_request_id',
        'issue_to',
        'invoice_number',
        'store_id',
        'status',
        'status_two',
        'created_by',
        'issued_by',
        'received_at',
        'received_by',
        'reason',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'amount'      => 'decimal:2',
    ];

    public function scopeAwaitingReceipt($query)
    {
        return $query->where('status', 'issued')->where('status_two', 'issued');
    }

    public function scopeSubmittedForHodApproval($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($query) {
                $query->whereHas('itemRequest', function ($requestQuery) {
                    $requestQuery->where('status', 'pending issue');
                })->orWhere(function ($query) {
                    $query->whereNull('item_request_id')
                        ->whereNotNull('created_by');
                });
            });
    }

    public function satelliteStockReceipt()
    {
        return $this->belongsTo(SatelliteStockReceipt::class, 'satellite_stock_receipt_id');
    }

    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function itemcode()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function itemname()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function unitname()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_id', 'id');
    }

    public function storename()
    {
        return $this->belongsTo(Store::class, 'issue_to', 'id');
    }

    public function staffname()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function authorised()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }

    public function issuefrom()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
