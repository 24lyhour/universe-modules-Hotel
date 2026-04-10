<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction;

use Modules\Hotel\Http\Resources\HotelReviewResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;

class GetHotelReviewIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = HotelReview::query()->with(['hotel', 'customer']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('hotel', fn ($h) => $h->where('name', 'like', "%{$search}%"));
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== 'all') {
            $query->where('is_active', $filters['is_active'] === 'true');
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        if (!empty($filters['hotel'])) {
            $query->where('hotel_id', $filters['hotel']);
        }

        $reviews = $query->latest()->paginate($perPage);

        return [
            'reviews' => [
                'data' => HotelReviewResource::collection($reviews)->resolve(),
                'meta' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => [
                'total' => HotelReview::count(),
                'active' => HotelReview::where('is_active', true)->count(),
                'inactive' => HotelReview::where('is_active', false)->count(),
                'pending_reply' => HotelReview::where('is_active', true)->whereNull('reply')->count(),
                'average_rating' => round(HotelReview::where('is_active', true)->avg('rating') ?? 0, 1),
                '5_star' => HotelReview::where('rating', 5)->count(),
                '4_star' => HotelReview::where('rating', 4)->count(),
                '3_star' => HotelReview::where('rating', 3)->count(),
                '2_star' => HotelReview::where('rating', 2)->count(),
                '1_star' => HotelReview::where('rating', 1)->count(),
            ],
            'hotels' => Hotel::orderBy('name')->get(['id', 'uuid', 'name']),
        ];
    }
}
