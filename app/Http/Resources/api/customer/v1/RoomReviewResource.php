<?php

namespace Modules\Hotel\Http\Resources\Api\Customer\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'room_id' => $this->room_id,
            'reviewer_name' => $this->customer?->name ?? $this->guest_name ?? 'Guest',
            'rating' => (float) $this->rating,
            'comment' => $this->comment,
            'reply' => $this->reply,
            'is_recommend' => (bool) $this->is_recommend,
            'helpful_count' => (int) $this->helpful_count,
            'created_at' => $this->created_at,
        ];
    }
}
