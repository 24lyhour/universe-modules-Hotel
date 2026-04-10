<?php

namespace Modules\Hotel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'hotel' => $this->whenLoaded('hotel', fn () => [
                'id' => $this->hotel->id,
                'uuid' => $this->hotel->uuid,
                'name' => $this->hotel->name,
            ]),
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'avatar' => $this->customer->avatar,
            ]),
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'reply' => $this->reply,
            'replied_at' => $this->replied_at,
            'images' => $this->images,
            'is_recommend' => $this->is_recommend,
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'helpful_count' => $this->helpful_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
