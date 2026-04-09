<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelCategory;

class HotelCategoryService
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = HotelCategory::query()->withCount('hotels');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('sort_order')->latest()->paginate($perPage);
    }

    public function find(string $uuid): ?HotelCategory
    {
        return HotelCategory::withCount('hotels')->where('uuid', $uuid)->first();
    }

    public function create(array $data): HotelCategory
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = (string) Str::uuid();
            $data['slug'] = Str::slug($data['name']);

            return HotelCategory::create($data);
        });
    }

    public function update(HotelCategory $category, array $data): bool
    {
        if (isset($data['name']) && $category->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $category->update($data);
    }

    public function delete(HotelCategory $category): bool
    {
        return $category->delete();
    }

    public function toggleActive(HotelCategory $category): bool
    {
        $category->is_active = !$category->is_active;

        return $category->save();
    }

    public function restore(string $uuid): bool
    {
        $category = HotelCategory::onlyTrashed()->where('uuid', $uuid)->first();

        return $category ? $category->restore() : false;
    }

    public function forceDelete(string $uuid): bool
    {
        $category = HotelCategory::onlyTrashed()->where('uuid', $uuid)->first();

        return $category ? $category->forceDelete() : false;
    }

    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        return HotelCategory::onlyTrashed()->latest('deleted_at')->paginate($perPage);
    }

    public function bulkDelete(array $uuids): int
    {
        return HotelCategory::whereIn('uuid', $uuids)->delete();
    }
}
