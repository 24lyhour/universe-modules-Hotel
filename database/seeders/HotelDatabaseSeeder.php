<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;

class HotelDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // province seeder
            ProvinceSeeder::class,

            // hotel seeder 
            HotelSeeder::class,

            // hotel review seeder
            HotelReviewSeeder::class,

        ]);
    }
}
