<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Http\Resources\Api\Customer\V1\HotelResource;
use Modules\Hotel\Http\Resources\Api\Customer\V1\RoomResource;
use Modules\Hotel\Http\Resources\Api\Customer\V1\ProvinceResource;
use Modules\Hotel\Http\Resources\Api\Customer\V1\HotelCategoryResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Province;
use Modules\Hotel\Services\HotelService;

class HotelApiController extends Controller
{
    public function __construct(
        protected HotelService $hotelService
    ) {}

    /**
     * List all hotels with filters
     */
    public function index(): JsonResponse
    {
        $filters = request()->only([
            'search',
            'city',
            'province_id',
            'category',
            'star_rating',
            'min_price',
            'max_price',
            'is_featured',
            'amenities',
        ]);
        $filters['status'] = 'active';

        $perPage = request()->integer('per_page', 15);
        $hotels = $this->hotelService->paginate($perPage, $filters);

        return HotelResource::collection($hotels)->response();
    }

    /**
     * Get single hotel with full details
     */
    public function show(Hotel $hotel): JsonResponse
    {
        $hotel->load([
            'category',
            'province',
            'rooms' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order'),
            'reviews' => fn ($q) => $q->where('status', 'approved')->latest(),
            'user',
        ]);

        return response()->json([
            'data' => new HotelResource($hotel),
        ]);
    }

    /**
     * Get rooms for a hotel
     */
    public function rooms(Hotel $hotel): JsonResponse
    {
        $rooms = $hotel->rooms()
            ->where('status', 'active')
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->paginate(request()->integer('per_page', 15));

        return RoomResource::collection($rooms)->response();
    }

    /**
     * Get hotel categories (property types)
     */
    public function categories(): JsonResponse
    {
        $categories = HotelCategory::active()
            ->withCount('hotels')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'uuid' => $c->uuid,
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => $c->icon,
                'hotels_count' => $c->hotels_count,
            ]),
        ]);
    }

    /**
     * Get all amenities
     */
    public function amenities(): JsonResponse
    {
        $amenities = Amenity::active()
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get(['id', 'uuid', 'name', 'slug', 'icon', 'group']);

        $grouped = $amenities->groupBy('group');

        return response()->json([
            'data' => $grouped->map(fn ($group, $groupName) => [
                'group' => $groupName,
                'amenities' => \Modules\Hotel\Http\Resources\Api\Customer\V1\AmenityResource::collection($group),
            ])->values(),
        ]);
    }

    /**
     * Get provinces with hotel counts
     */
    public function provinces(): JsonResponse
    {
        $provinces = Province::active()
            ->withCount('hotels')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'uuid' => $p->uuid,
                'name' => $p->name,
                'name_kh' => $p->name_kh,
                'code' => $p->code,
                'region' => $p->region,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'hotels_count' => $p->hotels_count,
            ]);

        return response()->json([
            'data' => $provinces,
        ]);
    }

    /**
     * Get featured hotels
     */
    public function featured(): JsonResponse
    {
        $hotels = Hotel::active()
            ->featured()
            ->with('category', 'province')
            ->limit(request()->integer('limit', 10))
            ->latest()
            ->get();

        return response()->json([
            'data' => HotelResource::collection($hotels),
        ]);
    }

    /**
     * Get cities with hotel counts
     */
    public function cities(): JsonResponse
    {
        $cities = Hotel::active()
            ->select('city')
            ->selectRaw('COUNT(*) as hotels_count')
            ->selectRaw('MIN(min_price) as min_price')
            ->selectRaw('MAX(max_price) as max_price')
            ->groupBy('city')
            ->orderByDesc('hotels_count')
            ->get();

        return response()->json([
            'data' => $cities,
        ]);
    }
}
