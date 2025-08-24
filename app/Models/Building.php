<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="BuildingMin",
 *     type="object",
 *     title="BuildingMin",
 *     description="Минимальная информация о здании",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="address", type="string", example="ул. Примерная, д. 123"),
 *     @OA\Property(
 *         property="location",
 *         type="object",
 *         @OA\Property(property="longitude", type="number", format="float", example=37.617634),
 *         @OA\Property(property="latitude", type="number", format="float", example=55.755826)
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Building",
 *     type="object",
 *     title="Building",
 *     description="Полная информация о здании",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/BuildingMin"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="organizations",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/OrganizationMin")
 *             )
 *         )
 *     }
 * )
 */
class Building extends Model
{
    use HasFactory;

    protected $casts = [
        'location' => Point::class,
    ];

    protected $fillable = [
        'address',
        'location',
    ];

    public function organizations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function scopeWithinRadius($query, $lat, $lng, $radius)
    {
        $pointWkt = "POINT($lng $lat)";

        return $query->whereRaw('ST_Distance(location, ST_GeogFromText(?)) <= ?', ["SRID=4326;$pointWkt", $radius]);
    }

    public function scopeWithinBoundingBox($query, $minLat, $minLng, $maxLat, $maxLng)
    {
        if (($maxLng - $minLng) > 180 || ($maxLat - $minLat) > 90) {
            return $query->whereRaw('ST_X(location::geometry) between ? and ?', [$minLng, $maxLng])
                ->whereRaw('ST_Y(location::geometry) between ? and ?', [$minLat, $maxLat]);
        } else {
            $wkt = sprintf('POLYGON((%f %f, %f %f, %f %f, %f %f, %f %f))',
                $minLng, $minLat,
                $maxLng, $minLat,
                $maxLng, $maxLat,
                $minLng, $maxLat,
                $minLng, $minLat
            );

            return $query->whereRaw('ST_Intersects(location, ST_GeogFromText(?))', ["SRID=4326;$wkt"]);
        }
    }
}
