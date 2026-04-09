<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Models\Hotel;

class ToggleHotelFeaturedAction
{
    public function execute(Hotel $hotel): Hotel
    {
        $hotel->is_featured = !$hotel->is_featured;
        $hotel->save();

        return $hotel;
    }
}
