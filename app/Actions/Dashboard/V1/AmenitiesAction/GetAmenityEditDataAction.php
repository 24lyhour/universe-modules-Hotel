<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Modules\Hotel\Http\Resources\AmenityResource;
use Modules\Hotel\Models\Amenity;

class GetAmenityEditDataAction
{
    public function execute(Amenity $amenity): array
    {
        return [
            'amenity' => (new AmenityResource($amenity))->resolve(),
        ];
    }
}
