<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Modules\Hotel\Models\HotelCategory;

class ToggleHotelCategoryStatusAction
{
    public function execute(HotelCategory $category): HotelCategory
    {
        $category->is_active = !$category->is_active;
        $category->save();

        return $category;
    }
}
