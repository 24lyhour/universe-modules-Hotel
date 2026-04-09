<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Illuminate\Support\Str;
use Modules\Hotel\Models\Amenity;

class UpdateAmenityAction
{
    public function execute(Amenity $amenity, array $data): Amenity
    {
        if (isset($data['name']) && $amenity->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
        }

        $amenity->update($data);

        return $amenity->fresh();
    }
}
