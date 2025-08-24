<?php

namespace App\Http\Resources\Api\Building;

use App\Http\Resources\Api\Organization\OrganizationMinResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
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
            'address' => $this->address,
            'location' => [
                'longitude' => $this->location->getLongitude(),
                'latitude' => $this->location->getLatitude(),
            ],
            'organizations' => OrganizationMinResource::collection($this->organizations)->resolve()
        ];
    }
}
