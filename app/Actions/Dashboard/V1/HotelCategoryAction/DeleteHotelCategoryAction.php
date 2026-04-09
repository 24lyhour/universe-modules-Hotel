<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Modules\Hotel\Models\HotelCategory;

class DeleteHotelCategoryAction
{
    public function execute(HotelCategory $category): bool
    {
        return $category->delete();
    }
}
