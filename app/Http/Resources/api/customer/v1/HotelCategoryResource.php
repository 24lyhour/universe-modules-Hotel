<?php

namespace Modules\Hotel\Http\Resources\Api\Customer\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'hotels_count' => $this->whenCounted('hotels'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
