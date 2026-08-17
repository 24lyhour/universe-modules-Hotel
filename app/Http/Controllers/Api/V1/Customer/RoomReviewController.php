<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Hotel\Http\Resources\Api\Customer\V1\RoomReviewResource;
use Modules\Hotel\Models\Room;

class RoomReviewController extends Controller
{
    /**
     * Get reviews for a specific room.
     */
    public function index(Room $room): JsonResponse
    {
        $reviews = $room->reviews()
            ->with('customer')
            ->active()
            ->orderByDesc('created_at')
            ->paginate(request()->integer('per_page', 10));

        return RoomReviewResource::collection($reviews)->response();
    }

    /**
     * Get review statistics for a room.
     */
    public function stats(Room $room): JsonResponse
    {
        $reviews = $room->reviews()->active()->get();

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
     * Store a new room review (requires auth).
     */
    public function store(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);

        $review = $room->reviews()->create([
            'customer_id' => $request->user()->id,
            'guest_name' => $request->user()->name ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_active' => false, // pending moderation
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => new RoomReviewResource($review->load('customer')),
        ], 201);
    }
}
