<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelCategory;

class CreateHotelCategoryAction
{
    public function execute(array $data): HotelCategory
    {
        $data['uuid'] = (string) Str::uuid();
        $data['slug'] = Str::slug($data['name']);

        return HotelCategory::create($data);
    }
}
