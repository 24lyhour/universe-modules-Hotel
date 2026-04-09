<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction;

use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\HotelCategory;

class BulkDeleteHotelCategoriesAction
{
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $category = HotelCategory::where('uuid', $uuid)->first();

                if ($category) {
                    $category->delete();
                    $deleted++;
                } else {
                    $failed++;
                }
            }
        });

        return [
            'deleted' => $deleted,
            'failed' => $failed,
        ];
    }
}
