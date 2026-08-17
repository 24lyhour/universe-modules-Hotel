<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Modules\Hotel\Models\RoomPolicies;

class ToggleRoomPolicyStatusAction
{
    public function execute(RoomPolicies $policy): RoomPolicies
    {
        $policy->is_active = !$policy->is_active;
        $policy->save();

        return $policy;
    }
}
