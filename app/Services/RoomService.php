<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Room;

class RoomService
{
    public function paginate(Hotel $hotel, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $hotel->rooms()->getQuery();

        $this->applyFilters($query, $filters);

        return $query->orderBy('sort_order')->latest()->paginate($perPage);
    }

    public function find(string $uuid): ?Room
    {
        return Room::with('hotel')->where('uuid', $uuid)->first();
    }

    public function create(Hotel $hotel, array $data): Room
    {
        return DB::transaction(function () use ($hotel, $data) {
            $data['uuid'] = (string) Str::uuid();
            $data['hotel_id'] = $hotel->id;

            return Room::create($data);
        });
    }

    public function update(Room $room, array $data): bool
    {
        return $room->update($data);
    }

    public function delete(Room $room): bool
    {
        return $room->delete();
    }

    public function restore(string $uuid): bool
    {
        $room = Room::onlyTrashed()->where('uuid', $uuid)->first();

        if (!$room) {
            return false;
        }

        return $room->restore();
    }

    public function forceDelete(string $uuid): bool
    {
        $room = Room::onlyTrashed()->where('uuid', $uuid)->first();

        if (!$room) {
            return false;
        }

        return $room->forceDelete();
    }

    public function getTrashed(Hotel $hotel, int $perPage = 15): LengthAwarePaginator
    {
        return Room::onlyTrashed()
            ->where('hotel_id', $hotel->id)
            ->latest('deleted_at')
            ->paginate($perPage);
    }

    public function toggleAvailability(Room $room): bool
    {
        $room->is_available = !$room->is_available;

        return $room->save();
    }

    public function bulkDelete(array $uuids): int
    {
        return Room::whereIn('uuid', $uuids)->delete();
    }

    public function reorder(array $orderedUuids): void
    {
        foreach ($orderedUuids as $index => $uuid) {
            Room::where('uuid', $uuid)->update(['sort_order' => $index]);
        }
    }

    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('room_type', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['room_type'])) {
            $query->where('room_type', $filters['room_type']);
        }

        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
    }
}
