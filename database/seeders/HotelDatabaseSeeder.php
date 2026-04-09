<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;

class HotelDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            HotelSeeder::class,
        ]);
    }
}
