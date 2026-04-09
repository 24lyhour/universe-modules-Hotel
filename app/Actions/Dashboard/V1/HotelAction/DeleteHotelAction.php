<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Models\Hotel;

class DeleteHotelAction
{
    public function execute(Hotel $hotel): bool
    {
        return $hotel->delete();
    }
}
