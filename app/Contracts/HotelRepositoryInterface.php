<?php

namespace Modules\Hotel\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Hotel\Models\Hotel;

interface HotelRepositoryInterface
{
    /**
     * Get all hotels.
     */
    public function all(): Collection;

    /**
     * Get paginated hotels.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a hotel by ID.
     */
    public function find(int $id): ?Hotel;

    /**
     * Find a hotel by slug.
     */
    public function findBySlug(string $slug): ?Hotel;

    /**
     * Create a new hotel.
     */
    public function create(array $data): Hotel;

    /**
     * Update a hotel.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a hotel.
     */
    public function delete(int $id): bool;

    /**
     * Get active hotels.
     */
    public function getActive(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get hotels by city.
     */
    public function getByCity(string $city, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get hotels by star rating.
     */
    public function getByStarRating(int $rating, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get hotels by user ID.
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Search hotels by name or city.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}
