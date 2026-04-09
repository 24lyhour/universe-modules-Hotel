<?php

namespace Modules\Hotel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'provinces' => $this->provinces,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'star_rating' => $this->star_rating,
            'price_level' => $this->price_level,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'min_discount_price' => $this->min_discount_price,
            'max_discount_price' => $this->max_discount_price,
            'currency' => $this->currency,
            'logo_url' => $this->logo_url,
            'featured_image' => $this->featured_image,
            'total_rooms' => $this->total_rooms,
            'total_floors' => $this->total_floors,
            'gallery' => $this->gallery,
            'amenities' => $this->amenities,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'uuid' => $this->category->uuid,
                'name' => $this->category->name,
                'icon' => $this->category->icon,
            ]),
            'province' => $this->whenLoaded('province', fn () => [
                'id' => $this->province->id,
                'uuid' => $this->province->uuid,
                'name' => $this->province->name,
                'name_kh' => $this->province->name_kh,
                'code' => $this->province->code,
            ]),
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
            'rooms_count' => $this->whenCounted('rooms'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
