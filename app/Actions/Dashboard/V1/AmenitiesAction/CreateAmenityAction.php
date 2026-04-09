<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Illuminate\Support\Str;
use Modules\Hotel\Models\Amenity;

class CreateAmenityAction
{
    public function execute(array $data): Amenity
    {
        $data['uuid'] = (string) Str::uuid();
        $data['slug'] = Str::slug($data['name']);

        return Amenity::create($data);
    }
}
