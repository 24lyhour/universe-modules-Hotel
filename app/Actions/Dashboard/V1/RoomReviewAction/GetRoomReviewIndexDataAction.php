<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomReviewAction;

use Modules\Hotel\Http\Resources\RoomReviewResource;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Models\RoomReview;

class GetRoomReviewIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = RoomReview::query()->with(['room.hotel', 'customer']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$search}%"));
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== 'all') {
            $query->where('is_active', $filters['is_active'] === 'true');
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        if (!empty($filters['room'])) {
            $query->where('room_id', $filters['room']);
        }

        $reviews = $query->latest()->paginate($perPage);

        return [
            'reviews' => [
                'data' => RoomReviewResource::collection($reviews)->resolve(),
                'meta' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => [
                'total' => RoomReview::count(),
                'active' => RoomReview::where('is_active', true)->count(),
                'inactive' => RoomReview::where('is_active', false)->count(),
                'pending_reply' => RoomReview::where('is_active', true)->whereNull('reply')->count(),
                'average_rating' => round(RoomReview::where('is_active', true)->avg('rating') ?? 0, 1),
                '5_star' => RoomReview::where('rating', 5)->count(),
                '4_star' => RoomReview::where('rating', 4)->count(),
                '3_star' => RoomReview::where('rating', 3)->count(),
                '2_star' => RoomReview::where('rating', 2)->count(),
                '1_star' => RoomReview::where('rating', 1)->count(),
            ],
            'rooms' => Room::orderBy('name')->get(['id', 'uuid', 'name']),
        ];
    }
}
