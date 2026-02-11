<?php

namespace Modules\Hotel\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Hotel\Contracts\HotelRepositoryInterface;
use Modules\Hotel\Models\Hotel;

class HotelRepository implements HotelRepositoryInterface
{
    public function __construct(
        protected Hotel $model
    ) {}

    /**
     * Get all hotels.
     */
    public function all(): Collection
    {
        return $this->model->with('user')->latest()->get();
    }

    /**
     * Get paginated hotels.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')->latest()->paginate($perPage);
    }

    /**
     * Find a hotel by ID.
     */
    public function find(int $id): ?Hotel
    {
        return $this->model->with('user')->find($id);
    }

    /**
     * Find a hotel by slug.
     */
    public function findBySlug(string $slug): ?Hotel
    {
        return $this->model->with('user')->where('slug', $slug)->first();
    }

    /**
     * Create a new hotel.
     */
    public function create(array $data): Hotel
    {
        return $this->model->create($data);
    }

    /**
     * Update a hotel.
     */
    public function update(int $id, array $data): bool
    {
        $hotel = $this->find($id);

        if (! $hotel) {
            return false;
        }

        return $hotel->update($data);
    }

    /**
     * Delete a hotel.
     */
    public function delete(int $id): bool
    {
        $hotel = $this->find($id);

        if (! $hotel) {
            return false;
        }

        return $hotel->delete();
    }

    /**
     * Get active hotels.
     */
    public function getActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')->active()->latest()->paginate($perPage);
    }

    /**
     * Get hotels by city.
     */
    public function getByCity(string $city, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')->byCity($city)->latest()->paginate($perPage);
    }

    /**
     * Get hotels by star rating.
     */
    public function getByStarRating(int $rating, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')->byStarRating($rating)->latest()->paginate($perPage);
    }

    /**
     * Get hotels by user ID.
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')->where('user_id', $userId)->latest()->paginate($perPage);
    }

    /**
     * Search hotels by name or city.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('user')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('city', 'like', "%{$query}%")
            ->orWhere('country', 'like', "%{$query}%")
            ->latest()
            ->paginate($perPage);
    }
}
