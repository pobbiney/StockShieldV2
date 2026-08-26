<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'status',
        'store_group',
    ];

    public const GENERAL_ADMIN_SATELLITE_NAME = 'General Admin Store (Satellite)';

    public function isGeneralAdminSatelliteHub(): bool
    {
        return $this->store_group === 'satellite'
            && strcasecmp(trim($this->name), self::GENERAL_ADMIN_SATELLITE_NAME) === 0;
    }
}
