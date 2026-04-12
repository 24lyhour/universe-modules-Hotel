<?php

namespace Modules\Hotel\Http\Resources\Api\Customer\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'hotel_id' => $this->hotel_id,
            'name' => $this->name,
            'room_type' => $this->room_type,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount_price' => (float) $this->discount_price,
            'capacity' => $this->capacity,
            'bed_type' => $this->bed_type,
            'bed_count' => $this->bed_count,
            'room_available_count' => $this->room_available_count,
            'bathroom_count' => $this->bathroom_count,
            'room_size' => $this->room_size,
            'view' => $this->view,
            'amenities' => $this->amenities,
            'images' => $this->images,
            'is_available' => $this->is_available,
            'hotel' => $this->whenLoaded('hotel', fn () => [
                'id' => $this->hotel->id,
                'uuid' => $this->hotel->uuid,
                'name' => $this->hotel->name,
                'city' => $this->hotel->city,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
