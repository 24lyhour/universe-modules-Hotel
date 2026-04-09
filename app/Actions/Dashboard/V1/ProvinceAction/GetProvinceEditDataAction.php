<?php

namespace Modules\Hotel\Actions\Dashboard\V1\ProvinceAction;

use Modules\Hotel\Http\Resources\ProvinceResource;
use Modules\Hotel\Models\Province;

class GetProvinceEditDataAction
{
    public function execute(Province $province): array
    {
        return [
            'province' => (new ProvinceResource($province))->resolve(),
        ];
    }
}
