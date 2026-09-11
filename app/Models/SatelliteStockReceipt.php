<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatelliteStockReceipt extends Model
{
    protected $table = 'satellite_stock_receipts';

    protected $fillable = [
        'item_issue_id',
        'satellite_item_issue_id',
        'satellite_stock_entry_id',
        'source_type',
        'item_request_id',
        'stock_id',
        'item_id',
        'batch_number',
        'qty',
        'amount',
        'expiry_date',
        'purchase_order',
        'supplier_id',
        'store_id',
        'central_store_id',
        'requisition_no',
        'invoice_number',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function itemIssue()
    {
        return $this->belongsTo(ItemIssue::class, 'item_issue_id');
    }

    public function satelliteStockEntry()
    {
        return $this->belongsTo(SatelliteStockEntry::class, 'satellite_stock_entry_id');
    }

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

    public function supname()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function storename()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function staffname()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function centralStore()
    {
        return $this->belongsTo(Store::class, 'central_store_id');
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }
}
