<?php

namespace Modules\Hotel\Actions\Dashboard\V1\ProvinceAction;

use Modules\Hotel\Http\Resources\ProvinceResource;
use Modules\Hotel\Models\Province;

class GetProvinceIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Province::query()->withCount('hotels');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_kh', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        $provinces = $query->orderBy('sort_order')->latest()->paginate($perPage);

        return [
            'provinces' => [
                'data' => ProvinceResource::collection($provinces)->resolve(),
                'meta' => [
                    'current_page' => $provinces->currentPage(),
                    'last_page' => $provinces->lastPage(),
                    'per_page' => $provinces->perPage(),
                    'total' => $provinces->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => [
                'total' => Province::count(),
                'active' => Province::where('is_active', true)->count(),
                'inactive' => Province::where('is_active', false)->count(),
            ],
        ];
    }
}
