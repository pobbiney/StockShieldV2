<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];

      public function itemcode()
	{
		return $this->belongsTo(Item::class, 'item_id' ,'id'); // 'itemID' is the foreign key
	}

     public function itemname()
	{
		return $this->belongsTo(Item::class, 'item_id' ,'id'); // 'itemID' is the foreign key
	}
     public function supname()
	{
		return $this->belongsTo(Supplier::class, 'supplier_id' ,'id'); // 'itemID' is the foreign key
	}

	 public function storename()
	{
		return $this->belongsTo(Store::class, 'store_id' ,'id'); // 'itemID' is the foreign key
	}

	  public function unitname()
	{
		return $this->belongsTo(UnitOfMeasure::class, 'unit_id' ,'id'); // 'itemID' is the foreign key
	}

	 public function staffname()
	{
		return $this->belongsTo(User::class, 'created_by' ,'id'); //  
	}
    


}
