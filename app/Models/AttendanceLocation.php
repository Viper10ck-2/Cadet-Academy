<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLocation extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if given coordinates are within the allowed radius of this location.
     */
    public function isWithinRadius(float $lat, float $lng): bool
    {
        $distance = $this->haversineDistance($this->latitude, $this->longitude, $lat, $lng);
        return $distance <= $this->radius_meters;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in meters.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2 +
            cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
