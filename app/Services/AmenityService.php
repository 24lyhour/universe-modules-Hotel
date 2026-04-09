<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Amenity;

class AmenityService
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Amenity::query()->withCount('hotels');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        return $query->orderBy('sort_order')->latest()->paginate($perPage);
    }

    public function find(string $uuid): ?Amenity
    {
        return Amenity::withCount('hotels')->where('uuid', $uuid)->first();
    }

    public function create(array $data): Amenity
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = (string) Str::uuid();
            $data['slug'] = Str::slug($data['name']);

            return Amenity::create($data);
        });
    }

    public function update(Amenity $amenity, array $data): bool
    {
        if (isset($data['name']) && $amenity->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $amenity->update($data);
    }

    public function delete(Amenity $amenity): bool
    {
        return $amenity->delete();
    }

    public function toggleActive(Amenity $amenity): bool
    {
        $amenity->is_active = !$amenity->is_active;

        return $amenity->save();
    }

    public function restore(string $uuid): bool
    {
        $amenity = Amenity::onlyTrashed()->where('uuid', $uuid)->first();

        return $amenity ? $amenity->restore() : false;
    }

    public function forceDelete(string $uuid): bool
    {
        $amenity = Amenity::onlyTrashed()->where('uuid', $uuid)->first();

        return $amenity ? $amenity->forceDelete() : false;
    }

    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        return Amenity::onlyTrashed()->latest('deleted_at')->paginate($perPage);
    }

    public function bulkDelete(array $uuids): int
    {
        return Amenity::whereIn('uuid', $uuids)->delete();
    }
}
