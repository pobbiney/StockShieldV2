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

    
];

 public function itemcode()
	{
		return $this->belongsTo(Item::class, 'item_id' ,'id'); // 'itemID' is the foreign key
	}

     public function itemname()
	{
		return $this->belongsTo(Item::class, 'item_id' ,'id'); // 'itemID' is the foreign key
	}

    public function storename()
	{
		return $this->belongsTo(Store::class, 'store_id' ,'id'); // 'itemID' is the foreign key
	}

     public function staffname()
	{
		return $this->belongsTo(User::class, 'created_by' ,'id'); // 'itemID' is the foreign key
	}

     public function authorised()
	{
		return $this->belongsTo(User::class, 'issued_by' ,'id'); // 'itemID' is the foreign key
	}
    

       public function issuefrom()
	{
		return $this->belongsTo(Store::class, 'store_id' ,'id'); // 'itemID' is the foreign key
	}
     
}
