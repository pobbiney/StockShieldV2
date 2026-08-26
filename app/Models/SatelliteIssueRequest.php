<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatelliteIssueRequest extends Model
{
    protected $fillable = [
        'satellite_stock_receipt_id',
        'item_id',
        'batch_number',
        'qty_requested',
        'qty_issued',
        'amount',
        'issue_no',
        'store_id',
        'issue_to_store_id',
        'ward_id',
        'status',
        'created_by',
        'issued_by',
        'issued_at',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function itemcode()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function itemname()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    public function issueToStore()
    {
        return $this->belongsTo(Store::class, 'issue_to_store_id', 'id');
    }

    public function destinationLabel(): string
    {
        if ($this->issue_to_store_id) {
            return $this->issueToStore->name ?? '—';
        }

        return $this->ward->name ?? '—';
    }

    public function storename()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function staffname()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }

    public function satelliteStockReceipt()
    {
        return $this->belongsTo(SatelliteStockReceipt::class, 'satellite_stock_receipt_id');
    }

    public function fulfillmentStatus(): string
    {
        if ($this->status === 'pending') {
            return 'draft';
        }

        if ($this->status === 'issued' || ($this->qty_issued > 0 && $this->qty_issued >= $this->qty_requested)) {
            return 'fulfilled';
        }

        if ($this->qty_issued > 0) {
            return 'partial';
        }

        return 'submitted';
    }

    public function fulfillmentLabel(): string
    {
        return match ($this->fulfillmentStatus()) {
            'draft'      => 'Draft',
            'fulfilled'  => 'Issued',
            'partial'    => 'Partially Issued',
            default      => 'Awaiting Issue',
        };
    }

    public function statusClass(): string
    {
        return match ($this->fulfillmentStatus()) {
            'draft'     => 'draft',
            'fulfilled' => 'fulfilled',
            'partial'   => 'partial',
            default     => 'submitted',
        };
    }
}
