<?php

namespace App\Http\Resources\Api\Organization;

use App\Http\Resources\Api\Activity\ActivityResource;
use App\Http\Resources\Api\Building\BuildingMinResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phones' => $this->phones->pluck('phone'),
            'building' => BuildingMinResource::make($this->building)->resolve(),
            'activities' => ActivityResource::collection($this->activities)->resolve(),
        ];
    }
}
