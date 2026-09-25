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
        'route_requisitions_to_central',
    ];

    protected $casts = [
        'is_requisition_hub'            => 'boolean',
        'route_requisitions_to_hub'     => 'boolean',
        'route_requisitions_to_central' => 'boolean',
    ];

    public const GENERAL_ADMIN_SATELLITE_NAME = 'General Admin Store (Satellite)';

    public const ADMIN_STORE_SATELLITE_NAME = 'Admin Store - Satellite';

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

    public function routesRequisitionsToCentral(): bool
    {
        if ($this->store_group !== 'satellite') {
            return true;
        }

        if ($this->isRequisitionHub()) {
            return false;
        }

        return $this->route_requisitions_to_central !== false;
    }

    public function requestsFromHubAndCentral(): bool
    {
        return $this->store_group === 'satellite'
            && $this->routesRequisitionsToHub()
            && $this->routesRequisitionsToCentral();
    }

    public static function requisitionHub(): ?self
    {
        return static::query()
            ->where('store_group', 'satellite')
            ->where('status', 'Active')
            ->where('is_requisition_hub', true)
            ->first();
    }

    public function isAdminStoreSatellite(): bool
    {
        return $this->store_group === 'satellite'
            && strcasecmp(trim($this->name), self::ADMIN_STORE_SATELLITE_NAME) === 0;
    }

    /**
     * Stores a requisition can request from: all central stores plus Admin Store - Satellite.
     */
    public static function requestFromStores()
    {
        $hubName = strtolower(self::ADMIN_STORE_SATELLITE_NAME);

        return static::query()
            ->where('status', 'Active')
            ->where(function ($query) use ($hubName) {
                $query->where('store_group', 'central')
                    ->orWhere(function ($satellite) use ($hubName) {
                        $satellite->where('store_group', 'satellite')
                            ->where(function ($hub) use ($hubName) {
                                $hub->where('is_requisition_hub', true)
                                    ->orWhereRaw('LOWER(TRIM(name)) = ?', [$hubName]);
                            });
                    });
            })
            ->orderByRaw("CASE WHEN store_group = 'central' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
    }
}
