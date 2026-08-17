<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Modules\Hotel\Models\RoomPolicies;

class UpdateRoomPolicyAction
{
    public function execute(RoomPolicies $policy, array $data): RoomPolicies
    {
        $policy->update($data);

        return $policy->fresh();
    }
}
