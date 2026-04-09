<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Http\Resources\HotelResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\Province;

class GetHotelEditDataAction
{
    public function execute(Hotel $hotel): array
    {
        $hotel->load(['category', 'province']);

        return [
            'hotel' => (new HotelResource($hotel))->resolve(),
            'categories' => HotelCategory::active()->orderBy('sort_order')->get(['id', 'uuid', 'name']),
            'provinces' => Province::active()->orderBy('sort_order')->get(['id', 'uuid', 'name', 'name_kh', 'code']),
            'statuses' => HotelStatusEnum::options(),
        ];
    }
}
