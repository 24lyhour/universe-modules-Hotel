<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Http\Resources\Api\Customer\V1\ProvinceResource;
use Modules\Hotel\Models\Province;

class ProvinceController extends Controller
{
    /**
     * Get all provinces with hotel counts
     */
    public function index(): JsonResponse
    {
        $provinces = Province::active()
            ->withCount('hotels')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => ProvinceResource::collection($provinces),
        ]);
    }

    /**
     * Get single province with its hotels
     */
    public function show(Province $province): JsonResponse
    {
        $province->load('hotels');

        return response()->json([
            'data' => new ProvinceResource($province),
        ]);
    }
}