<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Modules\Hotel\Http\Resources\HotelCategoryResource;
use Modules\Hotel\Models\HotelCategory;

class GetHotelCategoryEditDataAction
{
    public function execute(HotelCategory $category): array
    {
        return [
            'category' => (new HotelCategoryResource($category))->resolve(),
        ];
    }
}
