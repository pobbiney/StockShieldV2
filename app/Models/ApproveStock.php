<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApproveStock extends Model
{
    protected $fillable = [
    'stock_id',
    'item_id',
    'batch_number',
    'expiry_date',
    'qty',
    'amount',
    'purchase_order',
    'supplier_id',
    'store_id',
    'status',
    'created_by',
];
}
