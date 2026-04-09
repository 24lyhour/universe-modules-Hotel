<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Modules\Hotel\Http\Resources\AmenityResource;
use Modules\Hotel\Models\Amenity;

class GetAmenityIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Amenity::query()->withCount('hotels');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        $amenities = $query->orderBy('sort_order')->latest()->paginate($perPage);

        return [
            'amenities' => [
                'data' => AmenityResource::collection($amenities)->resolve(),
                'meta' => [
                    'current_page' => $amenities->currentPage(),
                    'last_page' => $amenities->lastPage(),
                    'per_page' => $amenities->perPage(),
                    'total' => $amenities->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => [
                'total' => Amenity::count(),
                'active' => Amenity::where('is_active', true)->count(),
                'inactive' => Amenity::where('is_active', false)->count(),
                'trashed' => Amenity::onlyTrashed()->count(),
            ],
        ];
    }
}
