<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Http\Resources\HotelResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\Province;

class GetHotelIndexDataAction
{
    public function execute(int $perPage = 15, array $filters = []): array
    {
        $query = Hotel::query()->with(['category', 'province', 'user', 'createdBy']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['province'])) {
            $query->where('province_id', $filters['province']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['category'])) {
            $query->where('hotel_category_id', $filters['category']);
        }

        if (!empty($filters['star_rating'])) {
            $query->where('star_rating', '>=', $filters['star_rating']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price_per_night', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price_per_night', '<=', $filters['max_price']);
        }

        $hotels = $query->latest()->paginate($perPage);

        return [
            'hotels' => [
                'data' => HotelResource::collection($hotels)->resolve(),
                'meta' => [
                    'current_page' => $hotels->currentPage(),
                    'last_page' => $hotels->lastPage(),
                    'per_page' => $hotels->perPage(),
                    'total' => $hotels->total(),
                ],
            ],
            'stats' => [
                'total' => Hotel::count(),
                'active' => Hotel::where('status', HotelStatusEnum::Active)->count(),
                'inactive' => Hotel::where('status', HotelStatusEnum::Inactive)->count(),
                'featured' => Hotel::where('is_featured', true)->count(),
                'trashed' => Hotel::onlyTrashed()->count(),
            ],
            'filters' => $filters,
            'categories' => HotelCategory::active()->orderBy('sort_order')->get(['id', 'uuid', 'name']),
            'provinces' => Province::active()->orderBy('sort_order')->get(['id', 'uuid', 'name', 'name_kh', 'code']),
            'statuses' => HotelStatusEnum::options(),
        ];
    }
}
