<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Http\Resources\Api\Customer\V1\AmenityResource;
use Modules\Hotel\Models\Amenity;

class AmenityController extends Controller
{
    /**
     * Get all amenities grouped by type
     */
    public function index(): JsonResponse
    {
        $amenities = Amenity::active()
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get();

        $grouped = $amenities->groupBy('group');

        return response()->json([
            'data' => $grouped->map(fn ($group) => [
                'group' => $group->first()->group,
                'amenities' => AmenityResource::collection($group),
            ])->values(),
        ]);
    }

    /**
     * Get single amenity
     */
    public function show(Amenity $amenity): JsonResponse
    {
        return response()->json([
            'data' => new AmenityResource($amenity),
        ]);
    }
}

