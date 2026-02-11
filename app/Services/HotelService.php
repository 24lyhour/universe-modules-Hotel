<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Modules\Hotel\Contracts\HotelRepositoryInterface;
use Modules\Hotel\Models\Hotel;

class HotelService
{
    public function __construct(
        protected HotelRepositoryInterface $hotelRepository
    ) {}

    /**
     * Get all hotels.
     */
    public function getAllHotels(): Collection
    {
        return $this->hotelRepository->all();
    }

    /**
     * Get paginated hotels.
     */
    public function getPaginatedHotels(int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotelRepository->paginate($perPage);
    }

    /**
     * Get a hotel by ID.
     */
    public function getHotelById(int $id): ?Hotel
    {
        return $this->hotelRepository->find($id);
    }

    /**
     * Get a hotel by slug.
     */
    public function getHotelBySlug(string $slug): ?Hotel
    {
        return $this->hotelRepository->findBySlug($slug);
    }

    /**
     * Create a new hotel.
     */
    public function createHotel(array $data): Hotel
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $data['user_id'] = auth()->id();

        return $this->hotelRepository->create($data);
    }

    /**
     * Update a hotel.
     */
    public function updateHotel(int $id, array $data): bool
    {
        if (isset($data['name'])) {
            $hotel = $this->hotelRepository->find($id);
            if ($hotel && $hotel->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }
        }

        return $this->hotelRepository->update($id, $data);
    }

    /**
     * Delete a hotel.
     */
    public function deleteHotel(int $id): bool
    {
        return $this->hotelRepository->delete($id);
    }

    /**
     * Get active hotels.
     */
    public function getActiveHotels(int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotelRepository->getActive($perPage);
    }

    /**
     * Get hotels by city.
     */
    public function getHotelsByCity(string $city, int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotelRepository->getByCity($city, $perPage);
    }

    /**
     * Get hotels by star rating.
     */
    public function getHotelsByStarRating(int $rating, int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotelRepository->getByStarRating($rating, $perPage);
    }

    /**
     * Get hotels by user.
     */
    public function getHotelsByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotelRepository->getByUserId($userId, $perPage);
    }

    /**
     * Search hotels.
     */
    public function searchHotels(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotelRepository->search($query, $perPage);
    }

    /**
     * Generate a unique slug for the hotel.
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (true) {
            $existingHotel = $this->hotelRepository->findBySlug($slug);

            if (! $existingHotel || ($excludeId && $existingHotel->id === $excludeId)) {
                break;
            }

            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
