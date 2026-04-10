<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction;

use Modules\Hotel\Http\Resources\HotelReviewResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;

class GetHotelReviewIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = HotelReview::query()->with(['hotel', 'user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('hotel', fn ($h) => $h->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
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
                'pending' => HotelReview::where('status', 'pending')->count(),
                'approved' => HotelReview::where('status', 'approved')->count(),
                'rejected' => HotelReview::where('status', 'rejected')->count(),
                'average_rating' => round(HotelReview::where('status', 'approved')->avg('rating') ?? 0, 1),
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
