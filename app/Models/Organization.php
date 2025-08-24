<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OrganizationMin",
 *     type="object",
 *     title="OrganizationMin",
 *     description="Минимальная информация об организации",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Название организации"),
 *     @OA\Property(
 *         property="phones",
 *         type="array",
 *         @OA\Items(type="string", example="+7 123 456-78-90")
 *     ),
 *     @OA\Property(
 *         property="activities",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Activity")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Organization",
 *     type="object",
 *     title="Organization",
 *     description="Полная информация об организации",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/OrganizationMin"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="building",
 *                 ref="#/components/schemas/BuildingMin"
 *             )
 *         )
 *     }
 * )
 */
class Organization extends Model
{
    use HasFactory;
    protected $fillable = [
      'name',
      'activity_id'
    ];

    public function building(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function phones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrganizationPhone::class);
    }

    public function activities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    }
}
