<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OrganizationPhone",
 *     type="object",
 *     title="OrganizationPhone",
 *     description="Телефон организации",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="phone", type="string", example="+7 123 456-78-90"),
 *     @OA\Property(property="organization_id", type="integer", format="int64", example=1)
 * )
 */
class OrganizationPhone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'organization_id',
    ];

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
