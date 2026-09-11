<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'status',
        'store_group',
        'is_requisition_hub',
        'route_requisitions_to_hub',
    ];

    protected $casts = [
        'is_requisition_hub'          => 'boolean',
        'route_requisitions_to_hub'   => 'boolean',
    ];

    public const GENERAL_ADMIN_SATELLITE_NAME = 'General Admin Store (Satellite)';

    public function isGeneralAdminSatelliteHub(): bool
    {
        return $this->store_group === 'satellite'
            && strcasecmp(trim($this->name), self::GENERAL_ADMIN_SATELLITE_NAME) === 0;
    }

    public function isRequisitionHub(): bool
    {
        return (bool) $this->is_requisition_hub;
    }

    public function routesRequisitionsToHub(): bool
    {
        return (bool) $this->route_requisitions_to_hub;
    }

    public static function requisitionHub(): ?self
    {
        return static::query()
            ->where('store_group', 'satellite')
            ->where('status', 'Active')
            ->where('is_requisition_hub', true)
            ->first();
    }
}
