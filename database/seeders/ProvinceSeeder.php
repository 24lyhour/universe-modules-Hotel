<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['name' => 'Phnom Penh',         'name_kh' => 'ភ្នំពេញ',           'code' => 'PP',  'region' => 'Central',    'latitude' => 11.5564, 'longitude' => 104.9282],
            ['name' => 'Siem Reap',          'name_kh' => 'សៀមរាប',           'code' => 'SR',  'region' => 'Northwest',  'latitude' => 13.3633, 'longitude' => 103.8564],
            ['name' => 'Sihanoukville',      'name_kh' => 'ព្រះសីហនុ',         'code' => 'SV',  'region' => 'Southwest',  'latitude' => 10.6093, 'longitude' => 103.5226],
            ['name' => 'Battambang',         'name_kh' => 'បាត់ដំបង',          'code' => 'BB',  'region' => 'Northwest',  'latitude' => 13.1023, 'longitude' => 103.1986],
            ['name' => 'Kampong Cham',       'name_kh' => 'កំពង់ចាម',          'code' => 'KC',  'region' => 'Central',    'latitude' => 11.9962, 'longitude' => 105.4636],
            ['name' => 'Kampong Chhnang',    'name_kh' => 'កំពង់ឆ្នាំង',        'code' => 'KCH', 'region' => 'Central',    'latitude' => 12.2505, 'longitude' => 104.6667],
            ['name' => 'Kampong Speu',       'name_kh' => 'កំពង់ស្ពឺ',          'code' => 'KS',  'region' => 'Central',    'latitude' => 11.4522, 'longitude' => 104.5186],
            ['name' => 'Kampong Thom',       'name_kh' => 'កំពង់ធំ',           'code' => 'KT',  'region' => 'Central',    'latitude' => 12.7112, 'longitude' => 104.8889],
            ['name' => 'Kampot',             'name_kh' => 'កំពត',              'code' => 'KP',  'region' => 'Southwest',  'latitude' => 10.6104, 'longitude' => 104.1810],
            ['name' => 'Kandal',             'name_kh' => 'កណ្ដាល',            'code' => 'KD',  'region' => 'Central',    'latitude' => 11.2235, 'longitude' => 104.9500],
            ['name' => 'Kep',                'name_kh' => 'កែប',               'code' => 'KE',  'region' => 'Southwest',  'latitude' => 10.4835, 'longitude' => 104.3165],
            ['name' => 'Koh Kong',           'name_kh' => 'កោះកុង',            'code' => 'KK',  'region' => 'Southwest',  'latitude' => 11.6150, 'longitude' => 103.0000],
            ['name' => 'Kratie',             'name_kh' => 'ក្រចេះ',             'code' => 'KR',  'region' => 'Northeast',  'latitude' => 12.4880, 'longitude' => 106.0190],
            ['name' => 'Mondulkiri',         'name_kh' => 'មណ្ឌលគិរី',          'code' => 'MK',  'region' => 'Northeast',  'latitude' => 12.4560, 'longitude' => 107.1875],
            ['name' => 'Oddar Meanchey',     'name_kh' => 'ឧត្តរមានជ័យ',        'code' => 'OM',  'region' => 'Northwest',  'latitude' => 14.1609, 'longitude' => 103.8162],
            ['name' => 'Pailin',             'name_kh' => 'ប៉ៃលិន',            'code' => 'PL',  'region' => 'Northwest',  'latitude' => 12.8490, 'longitude' => 102.6097],
            ['name' => 'Preah Vihear',       'name_kh' => 'ព្រះវិហារ',          'code' => 'PV',  'region' => 'North',      'latitude' => 13.7880, 'longitude' => 104.9790],
            ['name' => 'Prey Veng',          'name_kh' => 'ព្រៃវែង',            'code' => 'PG',  'region' => 'Southeast',  'latitude' => 11.4841, 'longitude' => 105.3246],
            ['name' => 'Pursat',             'name_kh' => 'ពោធិ៍សាត់',          'code' => 'PS',  'region' => 'Northwest',  'latitude' => 12.5337, 'longitude' => 103.9192],
            ['name' => 'Ratanakiri',         'name_kh' => 'រតនគិរី',           'code' => 'RK',  'region' => 'Northeast',  'latitude' => 13.7398, 'longitude' => 107.0013],
            ['name' => 'Stung Treng',        'name_kh' => 'ស្ទឹងត្រែង',         'code' => 'ST',  'region' => 'Northeast',  'latitude' => 13.5285, 'longitude' => 105.9689],
            ['name' => 'Svay Rieng',         'name_kh' => 'ស្វាយរៀង',           'code' => 'SG',  'region' => 'Southeast',  'latitude' => 11.0879, 'longitude' => 105.7993],
            ['name' => 'Takeo',              'name_kh' => 'តាកែវ',             'code' => 'TK',  'region' => 'Southwest',  'latitude' => 10.9908, 'longitude' => 104.7850],
            ['name' => 'Tboung Khmum',       'name_kh' => 'ត្បូងឃ្មុំ',          'code' => 'TBK', 'region' => 'Central',    'latitude' => 11.9600, 'longitude' => 105.6700],
            ['name' => 'Banteay Meanchey',   'name_kh' => 'បន្ទាយមានជ័យ',       'code' => 'BM',  'region' => 'Northwest',  'latitude' => 13.6531, 'longitude' => 102.5634],
        ];

        foreach ($provinces as $index => $data) {
            Province::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, [
                    'uuid' => (string) Str::uuid(),
                    'is_active' => true,
                    'sort_order' => $index,
                ])
            );
        }
    }
}
