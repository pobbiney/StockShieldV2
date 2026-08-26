<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $fillable = [
        'name',
        'store_id',
        'status',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
