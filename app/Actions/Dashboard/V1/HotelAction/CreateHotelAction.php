<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;

class CreateHotelAction
{
    public function execute(array $data): Hotel
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = (string) Str::uuid();
            $data['slug'] = $this->generateUniqueSlug($data['name']);
            $data['user_id'] = auth()->id();
            $data['created_by'] = auth()->id();

            return Hotel::create($data);
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
