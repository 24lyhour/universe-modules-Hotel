<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\RoomPolicies;

class BulkDeleteRoomPoliciesAction
{
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $policy = RoomPolicies::where('uuid', $uuid)->first();

                if ($policy) {
                    $policy->delete();
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
