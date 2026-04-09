<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Models\Hotel;

class HotelService
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Hotel::query()->with(['category', 'province', 'user', 'createdBy']);

        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    public function find(int|string $id): ?Hotel
    {
        return Hotel::with(['category', 'province', 'user', 'rooms', 'createdBy', 'updatedBy'])
            ->where('uuid', $id)
            ->orWhere('id', $id)
            ->first();
    }

    public function findBySlug(string $slug): ?Hotel
    {
        return Hotel::with(['category', 'user', 'rooms'])
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Hotel
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = (string) Str::uuid();
            $data['slug'] = $this->generateUniqueSlug($data['name']);
            $data['created_by'] = auth()->id();

            $hotel = Hotel::create($data);

            return $hotel;
        });
    }

    public function update(Hotel $hotel, array $data): bool
    {
        return DB::transaction(function () use ($hotel, $data) {
            if (isset($data['name']) && $hotel->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $hotel->id);
            }

            $data['updated_by'] = auth()->id();

            $updated = $hotel->update($data);

            $this->clearCache();

            return $updated;
        });
    }

    public function delete(Hotel $hotel): bool
    {
        $deleted = $hotel->delete();
        $this->clearCache();

        return $deleted;
    }

    public function restore(string $uuid): bool
    {
        $hotel = Hotel::onlyTrashed()->where('uuid', $uuid)->first();

        if (!$hotel) {
            return false;
        }

        $hotel->restore();
        $this->clearCache();

        return true;
    }

    public function forceDelete(string $uuid): bool
    {
        $hotel = Hotel::onlyTrashed()->where('uuid', $uuid)->first();

        if (!$hotel) {
            return false;
        }

        $hotel->forceDelete();
        $this->clearCache();

        return true;
    }

    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        return Hotel::onlyTrashed()
            ->with(['category', 'user'])
            ->latest('deleted_at')
            ->paginate($perPage);
    }

    public function getStats(): array
    {
        return Cache::remember('hotel_stats', 300, function () {
            return [
                'total' => Hotel::count(),
                'active' => Hotel::where('status', HotelStatusEnum::Active)->count(),
                'inactive' => Hotel::where('status', HotelStatusEnum::Inactive)->count(),
                'featured' => Hotel::where('is_featured', true)->count(),
                'trashed' => Hotel::onlyTrashed()->count(),
            ];
        });
    }

    public function toggleFeatured(Hotel $hotel): bool
    {
        $hotel->is_featured = !$hotel->is_featured;
        $saved = $hotel->save();
        $this->clearCache();

        return $saved;
    }

    public function updateStatus(Hotel $hotel, HotelStatusEnum $status): bool
    {
        $hotel->status = $status;
        $saved = $hotel->save();
        $this->clearCache();

        return $saved;
    }

    public function bulkDelete(array $uuids): int
    {
        $count = Hotel::whereIn('uuid', $uuids)->delete();
        $this->clearCache();

        return $count;
    }

    public function bulkRestore(array $uuids): int
    {
        $count = Hotel::onlyTrashed()->whereIn('uuid', $uuids)->restore();
        $this->clearCache();

        return $count;
    }

    public function emptyTrash(): int
    {
        $count = Hotel::onlyTrashed()->forceDelete();
        $this->clearCache();

        return $count;
    }

    protected function applyFilters($query, array $filters): void
    {
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
    }

    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (true) {
            $query = Hotel::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    protected function clearCache(): void
    {
        Cache::forget('hotel_stats');
    }
}
