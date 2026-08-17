<?php

namespace Modules\Hotel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'room' => $this->whenLoaded('room', fn () => [
                'id' => $this->room->id,
                'uuid' => $this->room->uuid,
                'name' => $this->room->name,
                'hotel' => $this->room->relationLoaded('hotel') && $this->room->hotel ? [
                    'id' => $this->room->hotel->id,
                    'uuid' => $this->room->hotel->uuid,
                    'name' => $this->room->hotel->name,
                ] : null,
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
