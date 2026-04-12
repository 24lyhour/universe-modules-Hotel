<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Http\Resources\Api\Customer\V1\HotelReviewResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;

class HotelReviewController extends Controller
{
    /**
     * Get reviews for a specific hotel
     */
    public function index(Hotel $hotel): JsonResponse
    {
        $reviews = $hotel->reviews()
            ->with('user')
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->paginate(request()->integer('per_page', 10));

        return HotelReviewResource::collection($reviews)->response();
    }

    /**
     * Get review statistics for a hotel
     */
    public function stats(Hotel $hotel): JsonResponse
    {
        $reviews = $hotel->reviews()->where('status', 'approved')->get();

        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->isEmpty() ? 0 : round($reviews->avg('rating'), 1),
            'ratings_breakdown' => [
                5 => $reviews->where('rating', 5)->count(),
                4 => $reviews->where('rating', 4)->count(),
                3 => $reviews->where('rating', 3)->count(),
                2 => $reviews->where('rating', 2)->count(),
                1 => $reviews->where('rating', 1)->count(),
            ],
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }

    /**
     * Store a new review (requires auth)
     */
    public function store(Request $request, Hotel $hotel): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);

        $review = $hotel->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => new HotelReviewResource($review),
        ], 201);
    }

    /**
     * Get single review
     */
    public function show(HotelReview $review): JsonResponse
    {
        return response()->json([
            'data' => new HotelReviewResource($review),
        ]);
    }
}
