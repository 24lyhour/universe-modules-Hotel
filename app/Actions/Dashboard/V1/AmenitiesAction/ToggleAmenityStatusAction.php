<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Modules\Hotel\Models\Amenity;

class ToggleAmenityStatusAction
{
    public function execute(Amenity $amenity): Amenity
    {
        $amenity->is_active = !$amenity->is_active;
        $amenity->save();

        return $amenity;
    }
}
