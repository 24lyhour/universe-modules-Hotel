<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction;

use Modules\Hotel\Models\RoomPolicies;

class CreateRoomPolicyAction
{
    public function execute(array $data): RoomPolicies
    {
        return RoomPolicies::create($data);
    }
}
