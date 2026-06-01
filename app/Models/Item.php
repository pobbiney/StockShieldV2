<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ApproveStock;

class Item extends Model


{
	   use HasFactory;

    protected $guarded = [];

      public function categoryname()
	{
		return $this->belongsTo(ItemCategory::class, 'cat_id' ,'id'); // 'itemID' is the foreign key
	}

      public function storename()
	{
		return $this->belongsTo(Store::class, 'store_id' ,'id'); // 'itemID' is the foreign key
	}

      public function unitname()
	{
		return $this->belongsTo(UnitOfMeasure::class, 'unit_id' ,'id'); // 'itemID' is the foreign key
	}
    public function approveStock()
	{
		return $this->hasMany(ApproveStock::class, 'item_id');
	}

	 
}
