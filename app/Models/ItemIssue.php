<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIssue extends Model
{

protected $fillable = [
    'stock_id',
    'satellite_stock_receipt_id',
    'item_id',
    'batch_number',
    'qty',
    'qty_requested',
    'amount',
    'requisition_no',
    'item_request_id',
    'issue_to',
    'inovice_number',
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
    ];

    public function scopeAwaitingReceipt($query)
    {
        return $query->where('status', 'issued')->where('status_two', 'issued');
    }

    /**
     * Issues submitted by the store manager and ready for HOD approval.
     * Requisition-linked lines require item_request status "pending issue".
     * Direct Issue Item entries (no requisition line) are included when created_by is set.
     */
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
        return $this->hasOne(SatelliteStockReceipt::class, 'item_issue_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }
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
		return $this->belongsTo(Store::class, 'issue_to' ,'id'); // 'itemID' is the foreign key
	}

     public function staffname()
	{
		return $this->belongsTo(User::class, 'created_by' ,'id'); // 'itemID' is the foreign key
	}

     public function authorised()
	{
		return $this->belongsTo(User::class, 'issued_by' ,'id'); // 'itemID' is the foreign key
	}

    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }
    

       public function issuefrom()
	{
		return $this->belongsTo(Store::class, 'store_id' ,'id'); // 'itemID' is the foreign key
	}
     

     

}
