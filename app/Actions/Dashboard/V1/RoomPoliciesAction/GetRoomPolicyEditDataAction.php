<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Modules\Hotel\Http\Resources\RoomPolicyResource;
use Modules\Hotel\Models\RoomPolicies;

class GetRoomPolicyEditDataAction
{
    public function execute(RoomPolicies $policy): array
    {
        return [
            'policy' => (new RoomPolicyResource($policy))->resolve(),
        ];
    }
}
