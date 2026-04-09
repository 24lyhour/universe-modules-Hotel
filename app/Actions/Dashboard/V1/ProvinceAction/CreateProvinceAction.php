<?php

namespace Modules\Hotel\Actions\Dashboard\V1\ProvinceAction;

use Illuminate\Support\Str;
use Modules\Hotel\Models\Province;

class CreateProvinceAction
{
    public function execute(array $data): Province
    {
        $data['uuid'] = (string) Str::uuid();

        return Province::create($data);
    }
}
