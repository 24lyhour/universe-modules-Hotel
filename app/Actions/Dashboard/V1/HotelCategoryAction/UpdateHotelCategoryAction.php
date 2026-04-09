<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelCategory;

class UpdateHotelCategoryAction
{
    public function execute(HotelCategory $category, array $data): HotelCategory
    {
        if (isset($data['name']) && $category->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return $category->fresh();
    }
}
