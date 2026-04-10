<?php

namespace Modules\Hotel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    private function getEffectiveDiscountPrice(): ?float
    {
        // Room's own discount takes priority
        if ($this->discount_price) {
            return (float) $this->discount_price;
        }

        // Fallback to hotel's discount percentage
        $hotel = $this->relationLoaded('hotel') ? $this->hotel : $this->resource->hotel;
        if ($hotel && $hotel->discount_percentage && $this->price) {
            return round((float) $this->price * (1 - $hotel->discount_percentage / 100), 2);
        }

        return null;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'total_room' => $this->total_room,
            'room_type' => $this->room_type,
            'room_number' => $this->room_number,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'effective_discount_price' => $this->getEffectiveDiscountPrice(),
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
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'hotel' => $this->whenLoaded('hotel', fn () => [
                'id' => $this->hotel->id,
                'uuid' => $this->hotel->uuid,
                'name' => $this->hotel->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
