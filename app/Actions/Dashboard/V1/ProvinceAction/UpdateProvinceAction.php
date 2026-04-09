<?php

namespace Modules\Hotel\Actions\Dashboard\V1\ProvinceAction;

use Modules\Hotel\Models\Province;

class UpdateProvinceAction
{
    public function execute(Province $province, array $data): Province
    {
        $province->update($data);

        return $province->fresh();
    }
}
