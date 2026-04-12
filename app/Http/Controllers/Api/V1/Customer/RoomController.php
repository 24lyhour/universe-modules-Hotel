<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Http\Resources\Api\Customer\V1\RoomResource;
use Modules\Hotel\Models\Room;

class RoomController extends Controller
{
    /**
     * Get all rooms (across all hotels)
     */
    public function index(): JsonResponse
    {
        $rooms = Room::active()
            ->with('hotel')
            ->where('is_available', true)
            ->paginate(request()->integer('per_page', 15));

        return RoomResource::collection($rooms)->response();
    }

    /**
     * Get single room
     */
    public function show(Room $room): JsonResponse
    {
        $room->load('hotel');

        return response()->json([
            'data' => new RoomResource($room),
        ]);
    }
}

