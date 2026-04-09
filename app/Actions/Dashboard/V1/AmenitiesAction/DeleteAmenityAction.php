<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Modules\Hotel\Models\Amenity;

class DeleteAmenityAction
{
    public function execute(Amenity $amenity): bool
    {
        return $amenity->delete();
    }
}
