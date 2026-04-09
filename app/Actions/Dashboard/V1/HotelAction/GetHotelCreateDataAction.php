<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\Province;

class GetHotelCreateDataAction
{
    public function execute(): array
    {
        return [
            'categories' => HotelCategory::active()->orderBy('sort_order')->get(['id', 'uuid', 'name']),
            'provinces' => Province::active()->orderBy('sort_order')->get(['id', 'uuid', 'name', 'name_kh', 'code']),
            'statuses' => HotelStatusEnum::options(),
        ];
    }
}
