<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Modules\Hotel\Http\Resources\HotelCategoryResource;
use Modules\Hotel\Models\HotelCategory;

class GetHotelCategoryIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = HotelCategory::query()->withCount('hotels');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        $categories = $query->orderBy('sort_order')->latest()->paginate($perPage);

        return [
            'categories' => [
                'data' => HotelCategoryResource::collection($categories)->resolve(),
                'meta' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => [
                'total' => HotelCategory::count(),
                'active' => HotelCategory::where('is_active', true)->count(),
                'inactive' => HotelCategory::where('is_active', false)->count(),
                'trashed' => HotelCategory::onlyTrashed()->count(),
            ],
        ];
    }
}
