<?php

namespace Modules\Hotel\Actions\Dashboard\V1\ProvinceAction;

use Modules\Hotel\Models\Province;

class ToggleProvinceStatusAction
{
    public function execute(Province $province): Province
    {
        $province->is_active = !$province->is_active;
        $province->save();

        return $province;
    }
}
