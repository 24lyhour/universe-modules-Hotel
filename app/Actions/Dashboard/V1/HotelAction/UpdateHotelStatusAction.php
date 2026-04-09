<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Models\Hotel;

class UpdateHotelStatusAction
{
    public function execute(Hotel $hotel, HotelStatusEnum $status): Hotel
    {
        $hotel->status = $status;
        $hotel->save();

        return $hotel;
    }
}
