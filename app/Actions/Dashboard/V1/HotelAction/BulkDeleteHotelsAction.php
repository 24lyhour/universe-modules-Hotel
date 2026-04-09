<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelAction;

use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\Hotel;

class BulkDeleteHotelsAction
{
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $hotel = Hotel::where('uuid', $uuid)->first();

                if ($hotel) {
                    $hotel->delete();
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
