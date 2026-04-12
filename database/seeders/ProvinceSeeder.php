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
            ['name' => 'Phnom Penh',         'name_kh' => 'ភ្នំពេញ',           'code' => 'PP',  'region' => 'Central',    'latitude' => 11.5564, 'longitude' => 104.9282, 'logo_url' => 'https://images.unsplash.com/photo-1585399781414-eaac27fe8b6e?w=400&h=400&fit=crop'],
            ['name' => 'Siem Reap',          'name_kh' => 'សៀមរាប',           'code' => 'SR',  'region' => 'Northwest',  'latitude' => 13.3633, 'longitude' => 103.8564, 'logo_url' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=400&h=400&fit=crop'],
            ['name' => 'Sihanoukville',      'name_kh' => 'ព្រះសីហនុ',         'code' => 'SV',  'region' => 'Southwest',  'latitude' => 10.6093, 'longitude' => 103.5226, 'logo_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=400&fit=crop'],
            ['name' => 'Battambang',         'name_kh' => 'បាត់ដំបង',          'code' => 'BB',  'region' => 'Northwest',  'latitude' => 13.1023, 'longitude' => 103.1986, 'logo_url' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400&h=400&fit=crop'],
            ['name' => 'Kampong Cham',       'name_kh' => 'កំពង់ចាម',          'code' => 'KC',  'region' => 'Central',    'latitude' => 11.9962, 'longitude' => 105.4636, 'logo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop'],
            ['name' => 'Kampong Chhnang',    'name_kh' => 'កំពង់ឆ្នាំង',        'code' => 'KCH', 'region' => 'Central',    'latitude' => 12.2505, 'longitude' => 104.6667, 'logo_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=400&fit=crop'],
            ['name' => 'Kampong Speu',       'name_kh' => 'កំពង់ស្ពឺ',          'code' => 'KS',  'region' => 'Central',    'latitude' => 11.4522, 'longitude' => 104.5186, 'logo_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&h=400&fit=crop'],
            ['name' => 'Kampong Thom',       'name_kh' => 'កំពង់ធំ',           'code' => 'KT',  'region' => 'Central',    'latitude' => 12.7112, 'longitude' => 104.8889, 'logo_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=400&fit=crop'],
            ['name' => 'Kampot',             'name_kh' => 'កំពត',              'code' => 'KP',  'region' => 'Southwest',  'latitude' => 10.6104, 'longitude' => 104.1810, 'logo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop'],
            ['name' => 'Kandal',             'name_kh' => 'កណ្ដាល',            'code' => 'KD',  'region' => 'Central',    'latitude' => 11.2235, 'longitude' => 104.9500, 'logo_url' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400&h=400&fit=crop'],
            ['name' => 'Kep',                'name_kh' => 'កែប',               'code' => 'KE',  'region' => 'Southwest',  'latitude' => 10.4835, 'longitude' => 104.3165, 'logo_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=400&fit=crop'],
            ['name' => 'Koh Kong',           'name_kh' => 'កោះកុង',            'code' => 'KK',  'region' => 'Southwest',  'latitude' => 11.6150, 'longitude' => 103.0000, 'logo_url' => 'https://images.unsplash.com/photo-1505142468610-359e7d316be0?w=400&h=400&fit=crop'],
            ['name' => 'Kratie',             'name_kh' => 'ក្រចេះ',             'code' => 'KR',  'region' => 'Northeast',  'latitude' => 12.4880, 'longitude' => 106.0190, 'logo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop'],
            ['name' => 'Mondulkiri',         'name_kh' => 'មណ្ឌលគិរី',          'code' => 'MK',  'region' => 'Northeast',  'latitude' => 12.4560, 'longitude' => 107.1875, 'logo_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=400&fit=crop'],
            ['name' => 'Oddar Meanchey',     'name_kh' => 'ឧត្តរមានជ័យ',        'code' => 'OM',  'region' => 'Northwest',  'latitude' => 14.1609, 'longitude' => 103.8162, 'logo_url' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400&h=400&fit=crop'],
            ['name' => 'Pailin',             'name_kh' => 'ប៉ៃលិន',            'code' => 'PL',  'region' => 'Northwest',  'latitude' => 12.8490, 'longitude' => 102.6097, 'logo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop'],
            ['name' => 'Preah Vihear',       'name_kh' => 'ព្រះវិហារ',          'code' => 'PV',  'region' => 'North',      'latitude' => 13.7880, 'longitude' => 104.9790, 'logo_url' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=400&h=400&fit=crop'],
            ['name' => 'Prey Veng',          'name_kh' => 'ព្រៃវែង',            'code' => 'PG',  'region' => 'Southeast',  'latitude' => 11.4841, 'longitude' => 105.3246, 'logo_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&h=400&fit=crop'],
            ['name' => 'Pursat',             'name_kh' => 'ពោធិ៍សាត់',          'code' => 'PS',  'region' => 'Northwest',  'latitude' => 12.5337, 'longitude' => 103.9192, 'logo_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=400&fit=crop'],
            ['name' => 'Ratanakiri',         'name_kh' => 'រតនគិរី',           'code' => 'RK',  'region' => 'Northeast',  'latitude' => 13.7398, 'longitude' => 107.0013, 'logo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop'],
            ['name' => 'Stung Treng',        'name_kh' => 'ស្ទឹងត្រែង',         'code' => 'ST',  'region' => 'Northeast',  'latitude' => 13.5285, 'longitude' => 105.9689, 'logo_url' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400&h=400&fit=crop'],
            ['name' => 'Svay Rieng',         'name_kh' => 'ស្វាយរៀង',           'code' => 'SG',  'region' => 'Southeast',  'latitude' => 11.0879, 'longitude' => 105.7993, 'logo_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&h=400&fit=crop'],
            ['name' => 'Takeo',              'name_kh' => 'តាកែវ',             'code' => 'TK',  'region' => 'Southwest',  'latitude' => 10.9908, 'longitude' => 104.7850, 'logo_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=400&fit=crop'],
            ['name' => 'Tboung Khmum',       'name_kh' => 'ត្បូងឃ្មុំ',          'code' => 'TBK', 'region' => 'Central',    'latitude' => 11.9600, 'longitude' => 105.6700, 'logo_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=400&fit=crop'],
            ['name' => 'Banteay Meanchey',   'name_kh' => 'បន្ទាយមានជ័យ',       'code' => 'BM',  'region' => 'Northwest',  'latitude' => 13.6531, 'longitude' => 102.5634, 'logo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop'],
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
