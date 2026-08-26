<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatelliteStockEntry extends Model
{
    protected $table = 'satellite_stock_entries';

    protected $fillable = [
        'item_id',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'supplier_id',
        'purchase_order',
        'waybill',
        'award_letter',
        'amount',
        'qty',
        'store_id',
        'barcode',
        'barcode_path',
        'comment',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

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
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function satelliteStockReceipt()
    {
        return $this->hasOne(SatelliteStockReceipt::class, 'satellite_stock_entry_id');
    }
}
