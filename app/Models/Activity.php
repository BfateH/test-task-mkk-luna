<?php

namespace App\Models;

use App\Services\ActivityService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Activity",
 *     type="object",
 *     title="Activity",
 *     description="Вид деятельности",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Еда"),
 * )
 */
class Activity extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function ($model) {
            $activityService = app(ActivityService::class);
            $activityService->checkDepthBeforeSaving($model);
        });

        static::deleted(function ($model) {
            $activityService = app(ActivityService::class);
            $activityService->changeChildrenParentIdAfterDelete($model);
        });
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    public function getAllDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    public function getAllDescendantsAndSelf()
    {
        return $this->getAllDescendants()->push($this);
    }
}
