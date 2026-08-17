<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Modules\Hotel\Http\Resources\RoomPolicyResource;
use Modules\Hotel\Models\RoomPolicies;

class GetRoomPolicyIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = RoomPolicies::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        $policies = $query->orderBy('sort_order')->latest()->paginate($perPage);

        return [
            'policies' => [
                'data' => RoomPolicyResource::collection($policies)->resolve(),
                'meta' => [
                    'current_page' => $policies->currentPage(),
                    'last_page' => $policies->lastPage(),
                    'per_page' => $policies->perPage(),
                    'total' => $policies->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => [
                'total' => RoomPolicies::count(),
                'active' => RoomPolicies::where('is_active', true)->count(),
                'inactive' => RoomPolicies::where('is_active', false)->count(),
                'trashed' => RoomPolicies::onlyTrashed()->count(),
            ],
        ];
    }
}
