<?php

namespace Modules\Hotel\Http\Resources\Api\Customer\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'name_kh' => $this->name_kh,
            'code' => $this->code,
            'logo_url' => $this->logo_url,
            'region' => $this->region,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hotels_count' => $this->whenCounted('hotels'),
            'hotels' => HotelResource::collection($this->whenLoaded('hotels')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
