<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;

class UpdateHotelAction
{
    public function execute(Hotel $hotel, array $data): Hotel
    {
        return DB::transaction(function () use ($hotel, $data) {
            if (isset($data['name']) && $hotel->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $hotel->id);
            }

            $data['updated_by'] = auth()->id();

            $hotel->update($data);

            return $hotel->fresh();
        });
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
}
