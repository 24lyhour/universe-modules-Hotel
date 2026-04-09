<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Http\Resources\HotelResource;
use Modules\Hotel\Models\Hotel;

class GetHotelShowDataAction
{
    public function execute(Hotel $hotel): array
    {
        $hotel->load(['category', 'province', 'user', 'rooms', 'createdBy', 'updatedBy']);

        return [
            'hotel' => (new HotelResource($hotel))->resolve(),
        ];
    }
}
