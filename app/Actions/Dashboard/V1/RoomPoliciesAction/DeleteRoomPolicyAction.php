<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Modules\Hotel\Models\RoomPolicies;

class DeleteRoomPolicyAction
{
    public function execute(RoomPolicies $policy): bool
    {
        return $policy->delete();
    }
}
