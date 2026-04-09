<?php

namespace Modules\Hotel\Actions\Dashboard\V1\ProvinceAction;

use Modules\Hotel\Models\Province;

class DeleteProvinceAction
{
    public function execute(Province $province): bool
    {
        return $province->delete();
    }
}
