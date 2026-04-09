<?php

namespace Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction;

use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\Amenity;

class BulkDeleteAmenitiesAction
{
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $amenity = Amenity::where('uuid', $uuid)->first();

                if ($amenity) {
                    $amenity->delete();
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
