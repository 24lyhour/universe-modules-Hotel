<?php

namespace Modules\Hotel\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\Province;
use Modules\Hotel\Models\Room;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user?->id ?? 1;

        // Seed Provinces
        $provinces = [
            ['name' => 'Phnom Penh', 'name_kh' => 'ភ្នំពេញ', 'code' => 'PP', 'region' => 'Central', 'latitude' => 11.5564, 'longitude' => 104.9282, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Siem Reap', 'name_kh' => 'សៀមរាប', 'code' => 'SR', 'region' => 'Northwest', 'latitude' => 13.3633, 'longitude' => 103.8564, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Sihanoukville', 'name_kh' => 'ព្រះសីហនុ', 'code' => 'SHV', 'region' => 'Southwest', 'latitude' => 10.6093, 'longitude' => 103.5228, 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Battambang', 'name_kh' => 'បាត់ដំបង', 'code' => 'BTB', 'region' => 'Northwest', 'latitude' => 13.1023, 'longitude' => 103.1986, 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Kampot', 'name_kh' => 'កំពត', 'code' => 'KPT', 'region' => 'Southwest', 'latitude' => 10.5940, 'longitude' => 104.1652, 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Kep', 'name_kh' => 'កែប', 'code' => 'KEP', 'region' => 'Southwest', 'latitude' => 10.4833, 'longitude' => 104.3167, 'is_active' => true, 'sort_order' => 6],
        ];

        foreach ($provinces as $data) {
            Province::firstOrCreate(['code' => $data['code']], $data);
        }

        // Seed Categories
        $categories = [
            ['name' => 'Luxury', 'icon' => 'Hotel', 'group' => 'tier', 'description' => 'Premium 5-star properties', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Boutique', 'icon' => 'Building2', 'group' => 'tier', 'description' => 'Unique design-focused stays', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Resort', 'icon' => 'Castle', 'group' => 'type', 'description' => 'Resort & spa properties', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Budget', 'icon' => 'Home', 'group' => 'tier', 'description' => 'Affordable accommodation', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Business', 'icon' => 'Briefcase', 'group' => 'type', 'description' => 'Business travel focused', 'is_active' => true, 'sort_order' => 5],
        ];

        $categoryModels = [];
        foreach ($categories as $data) {
            $categoryModels[] = HotelCategory::firstOrCreate(['name' => $data['name']], $data);
        }

        // Seed Amenities
        $amenities = [
            ['name' => 'Free WiFi', 'icon' => 'Wifi', 'group' => 'connectivity', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Swimming Pool', 'icon' => 'Pool', 'group' => 'recreation', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Spa & Wellness', 'icon' => 'Spa', 'group' => 'recreation', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Restaurant', 'icon' => 'Restaurant', 'group' => 'dining', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Gym', 'icon' => 'Gym', 'group' => 'recreation', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Airport Shuttle', 'icon' => 'Sparkles', 'group' => 'transport', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Parking', 'icon' => 'Parking', 'group' => 'transport', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Room Service', 'icon' => 'Coffee', 'group' => 'service', 'is_active' => true, 'sort_order' => 8],
            ['name' => 'Bar & Lounge', 'icon' => 'Sparkles', 'group' => 'dining', 'is_active' => true, 'sort_order' => 9],
            ['name' => 'Business Center', 'icon' => 'Sparkles', 'group' => 'service', 'is_active' => true, 'sort_order' => 10],
        ];

        $amenityModels = [];
        foreach ($amenities as $data) {
            $amenityModels[] = Amenity::firstOrCreate(['name' => $data['name']], $data);
        }

        // Placeholder images
        $hotelImages = [
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800&h=600&fit=crop',
        ];

        $roomImages = [
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1590490360182-c33d955f4a24?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&h=600&fit=crop',
        ];

        $logoImages = [
            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&h=200&fit=crop',
        ];

        $priceLevels = ['budget', 'mid-range', 'luxury'];

        // Seed Hotels
        $hotels = [
            // Phnom Penh (8 hotels)
            ['name' => 'Rosewood Phnom Penh', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 5, 'price' => 320, 'category' => 0, 'status' => 'active', 'featured' => true, 'rooms_count' => 175, 'floors' => 39, 'lat' => 11.5685, 'lng' => 104.9310, 'price_level' => 'luxury'],
            ['name' => 'Raffles Hotel Le Royal', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 5, 'price' => 280, 'category' => 0, 'status' => 'active', 'featured' => true, 'rooms_count' => 170, 'floors' => 4, 'lat' => 11.5728, 'lng' => 104.9218, 'price_level' => 'luxury'],
            ['name' => 'Hyatt Regency Phnom Penh', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 5, 'price' => 190, 'category' => 4, 'status' => 'active', 'featured' => false, 'rooms_count' => 247, 'floors' => 30, 'lat' => 11.5550, 'lng' => 104.9280, 'price_level' => 'luxury'],
            ['name' => 'The Plantation Urban Resort', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 4, 'price' => 120, 'category' => 1, 'status' => 'active', 'featured' => true, 'rooms_count' => 28, 'floors' => 3, 'lat' => 11.5580, 'lng' => 104.9270, 'price_level' => 'mid-range'],
            ['name' => 'Aquarius Hotel', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 4, 'price' => 85, 'category' => 4, 'status' => 'active', 'featured' => false, 'rooms_count' => 118, 'floors' => 12, 'lat' => 11.5620, 'lng' => 104.9190, 'price_level' => 'mid-range'],
            ['name' => 'Mad Monkey Hostel', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 2, 'price' => 15, 'category' => 3, 'status' => 'active', 'featured' => false, 'rooms_count' => 40, 'floors' => 3, 'lat' => 11.5700, 'lng' => 104.9235, 'price_level' => 'budget'],
            ['name' => 'Billabong Hotel', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 3, 'price' => 45, 'category' => 3, 'status' => 'inactive', 'featured' => false, 'rooms_count' => 25, 'floors' => 2, 'lat' => 11.5640, 'lng' => 104.9260, 'price_level' => 'budget'],
            ['name' => 'Baitong Hotel & Resort', 'city' => 'Phnom Penh', 'province' => 'PP', 'star_rating' => 4, 'price' => 95, 'category' => 2, 'status' => 'active', 'featured' => false, 'rooms_count' => 100, 'floors' => 8, 'lat' => 11.5510, 'lng' => 104.9330, 'price_level' => 'mid-range'],

            // Siem Reap (10 hotels)
            ['name' => 'Park Hyatt Siem Reap', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 5, 'price' => 350, 'category' => 0, 'status' => 'active', 'featured' => true, 'rooms_count' => 104, 'floors' => 3, 'lat' => 13.3540, 'lng' => 103.8560, 'price_level' => 'luxury'],
            ['name' => 'Sofitel Angkor Phokeethra', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 5, 'price' => 220, 'category' => 2, 'status' => 'active', 'featured' => true, 'rooms_count' => 238, 'floors' => 5, 'lat' => 13.3490, 'lng' => 103.8530, 'price_level' => 'luxury'],
            ['name' => 'Shinta Mani Angkor', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 5, 'price' => 180, 'category' => 1, 'status' => 'active', 'featured' => true, 'rooms_count' => 62, 'floors' => 3, 'lat' => 13.3550, 'lng' => 103.8590, 'price_level' => 'luxury'],
            ['name' => 'Viroth\'s Hotel', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 4, 'price' => 130, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 38, 'floors' => 3, 'lat' => 13.3510, 'lng' => 103.8620, 'price_level' => 'mid-range'],
            ['name' => 'Angkor Village Hotel', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 4, 'price' => 95, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 52, 'floors' => 2, 'lat' => 13.3580, 'lng' => 103.8540, 'price_level' => 'mid-range'],
            ['name' => 'Templation Hotel', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 4, 'price' => 110, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 30, 'floors' => 2, 'lat' => 13.3600, 'lng' => 103.8510, 'price_level' => 'mid-range'],
            ['name' => 'Golden Banana Resort', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 3, 'price' => 50, 'category' => 3, 'status' => 'active', 'featured' => false, 'rooms_count' => 45, 'floors' => 3, 'lat' => 13.3520, 'lng' => 103.8650, 'price_level' => 'budget'],
            ['name' => 'Siem Reap Hostel', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 2, 'price' => 12, 'category' => 3, 'status' => 'active', 'featured' => false, 'rooms_count' => 30, 'floors' => 2, 'lat' => 13.3570, 'lng' => 103.8570, 'price_level' => 'budget'],
            ['name' => 'Heritage Suites Hotel', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 5, 'price' => 250, 'category' => 0, 'status' => 'inactive', 'featured' => false, 'rooms_count' => 26, 'floors' => 2, 'lat' => 13.3560, 'lng' => 103.8600, 'price_level' => 'luxury'],
            ['name' => 'FCC Angkor by Avani', 'city' => 'Siem Reap', 'province' => 'SR', 'star_rating' => 4, 'price' => 140, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 80, 'floors' => 3, 'lat' => 13.3530, 'lng' => 103.8580, 'price_level' => 'mid-range'],

            // Sihanoukville (6 hotels)
            ['name' => 'Song Saa Private Island', 'city' => 'Sihanoukville', 'province' => 'SHV', 'star_rating' => 5, 'price' => 1200, 'category' => 0, 'status' => 'active', 'featured' => true, 'rooms_count' => 27, 'floors' => 2, 'lat' => 10.6050, 'lng' => 103.4950, 'price_level' => 'luxury'],
            ['name' => 'The Royal Sands Koh Rong', 'city' => 'Sihanoukville', 'province' => 'SHV', 'star_rating' => 5, 'price' => 280, 'category' => 2, 'status' => 'active', 'featured' => true, 'rooms_count' => 65, 'floors' => 3, 'lat' => 10.6120, 'lng' => 103.5180, 'price_level' => 'luxury'],
            ['name' => 'Sokha Beach Resort', 'city' => 'Sihanoukville', 'province' => 'SHV', 'star_rating' => 5, 'price' => 170, 'category' => 2, 'status' => 'active', 'featured' => false, 'rooms_count' => 191, 'floors' => 6, 'lat' => 10.6080, 'lng' => 103.5210, 'price_level' => 'mid-range'],
            ['name' => 'Independence Hotel', 'city' => 'Sihanoukville', 'province' => 'SHV', 'star_rating' => 4, 'price' => 120, 'category' => 2, 'status' => 'active', 'featured' => false, 'rooms_count' => 94, 'floors' => 8, 'lat' => 10.6030, 'lng' => 103.5250, 'price_level' => 'mid-range'],
            ['name' => 'Reef Resort', 'city' => 'Sihanoukville', 'province' => 'SHV', 'star_rating' => 3, 'price' => 55, 'category' => 3, 'status' => 'inactive', 'featured' => false, 'rooms_count' => 20, 'floors' => 2, 'lat' => 10.6100, 'lng' => 103.5300, 'price_level' => 'budget'],
            ['name' => 'Onederz Hostel', 'city' => 'Sihanoukville', 'province' => 'SHV', 'star_rating' => 2, 'price' => 10, 'category' => 3, 'status' => 'active', 'featured' => false, 'rooms_count' => 35, 'floors' => 3, 'lat' => 10.6070, 'lng' => 103.5230, 'price_level' => 'budget'],

            // Battambang (4 hotels)
            ['name' => 'Bambu Hotel', 'city' => 'Battambang', 'province' => 'BTB', 'star_rating' => 4, 'price' => 75, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 20, 'floors' => 2, 'lat' => 13.1050, 'lng' => 103.1960, 'price_level' => 'mid-range'],
            ['name' => 'Maisons Wat Kor', 'city' => 'Battambang', 'province' => 'BTB', 'star_rating' => 4, 'price' => 90, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 12, 'floors' => 2, 'lat' => 13.0950, 'lng' => 103.2010, 'price_level' => 'mid-range'],
            ['name' => 'Classy Hotel', 'city' => 'Battambang', 'province' => 'BTB', 'star_rating' => 3, 'price' => 35, 'category' => 3, 'status' => 'active', 'featured' => false, 'rooms_count' => 48, 'floors' => 5, 'lat' => 13.1020, 'lng' => 103.2000, 'price_level' => 'budget'],
            ['name' => 'Here Be Dragons', 'city' => 'Battambang', 'province' => 'BTB', 'star_rating' => 2, 'price' => 20, 'category' => 3, 'status' => 'inactive', 'featured' => false, 'rooms_count' => 15, 'floors' => 2, 'lat' => 13.1000, 'lng' => 103.1970, 'price_level' => 'budget'],

            // Kampot (4 hotels)
            ['name' => 'The Columns', 'city' => 'Kampot', 'province' => 'KPT', 'star_rating' => 4, 'price' => 85, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 16, 'floors' => 2, 'lat' => 10.5960, 'lng' => 104.1680, 'price_level' => 'mid-range'],
            ['name' => 'Rikitikitavi', 'city' => 'Kampot', 'province' => 'KPT', 'star_rating' => 3, 'price' => 55, 'category' => 1, 'status' => 'active', 'featured' => false, 'rooms_count' => 12, 'floors' => 3, 'lat' => 10.5930, 'lng' => 104.1670, 'price_level' => 'mid-range'],
            ['name' => 'Sabay Beach', 'city' => 'Kampot', 'province' => 'KPT', 'star_rating' => 3, 'price' => 40, 'category' => 2, 'status' => 'active', 'featured' => false, 'rooms_count' => 20, 'floors' => 2, 'lat' => 10.5910, 'lng' => 104.1640, 'price_level' => 'budget'],
            ['name' => 'Kampot River Lodge', 'city' => 'Kampot', 'province' => 'KPT', 'star_rating' => 2, 'price' => 25, 'category' => 3, 'status' => 'inactive', 'featured' => false, 'rooms_count' => 10, 'floors' => 1, 'lat' => 10.5950, 'lng' => 104.1660, 'price_level' => 'budget'],

            // Kep (3 hotels)
            ['name' => 'Knai Bang Chatt', 'city' => 'Kep', 'province' => 'KEP', 'star_rating' => 5, 'price' => 210, 'category' => 0, 'status' => 'active', 'featured' => true, 'rooms_count' => 18, 'floors' => 2, 'lat' => 10.4850, 'lng' => 104.3180, 'price_level' => 'luxury'],
            ['name' => 'Veranda Natural Resort', 'city' => 'Kep', 'province' => 'KEP', 'star_rating' => 4, 'price' => 80, 'category' => 2, 'status' => 'active', 'featured' => false, 'rooms_count' => 34, 'floors' => 2, 'lat' => 10.4870, 'lng' => 104.3150, 'price_level' => 'mid-range'],
            ['name' => 'Le Flamboyant Resort', 'city' => 'Kep', 'province' => 'KEP', 'star_rating' => 3, 'price' => 50, 'category' => 2, 'status' => 'active', 'featured' => false, 'rooms_count' => 22, 'floors' => 2, 'lat' => 10.4810, 'lng' => 104.3200, 'price_level' => 'budget'],
        ];

        $provinceMap = Province::pluck('id', 'code')->toArray();
        $roomTypes = ['standard', 'deluxe', 'superior', 'suite', 'family'];
        $roomNames = ['Standard Room', 'Deluxe Room', 'Superior Room', 'Suite', 'Family Room', 'Executive Room', 'Presidential Suite', 'Twin Room'];
        $bedTypes = ['king', 'queen', 'twin', 'double', 'single'];
        $views = ['City View', 'Pool View', 'Garden View', 'River View', 'Sea View', 'Mountain View', null];

        foreach ($hotels as $index => $data) {
            $category = $categoryModels[$data['category']];
            $province = $provinceMap[$data['province']] ?? null;

            // Pick images based on index
            $featuredImage = $hotelImages[$index % count($hotelImages)];
            $logo = $logoImages[$index % count($logoImages)];
            $gallery = array_slice($hotelImages, 0, rand(3, count($hotelImages)));
            shuffle($gallery);

            $hotel = Hotel::updateOrCreate(
                ['name' => $data['name']],
                [
                    'user_id' => $userId,
                    'hotel_category_id' => $category->id,
                    'description' => "<p>A beautiful {$data['star_rating']}-star hotel in {$data['city']}, Cambodia. Experience world-class hospitality and stunning views.</p><p>Our hotel offers premium amenities, spacious rooms, and exceptional service to make your stay unforgettable.</p>",
                    'address' => rand(1, 999) . ' ' . ['Preah Sihanouk Blvd', 'Norodom Blvd', 'Monivong Blvd', 'Street 240', 'Riverside'][rand(0, 4)],
                    'logo_url' => $logo,
                    'city' => $data['city'],
                    'country' => 'Cambodia',
                    'province_id' => $province,
                    'phone' => '+855 ' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                    'email' => strtolower(str_replace([' ', '\''], '', $data['name'])) . '@hotel.com',
                    'website' => 'https://www.' . strtolower(str_replace([' ', '\''], '', $data['name'])) . '.com',
                    'star_rating' => $data['star_rating'],
                    'price_level' => $data['price_level'],
                    // Legacy single-price column (kept for backward compat
                    // with old reads). New code reads min_price / max_price.
                    'price_per_night' => $data['price'],
                    'min_price' => round($data['price'] * 0.7, 2),
                    'max_price' => round($data['price'] * 1.5, 2),
                    'discount_price' => rand(0, 1) ? round($data['price'] * 0.7 * 0.85, 2) : null,
                    'discount_percentage' => rand(0, 1) ? rand(10, 25) : null,
                    'currency' => 'USD',
                    'featured_image' => $featuredImage,
                    'total_rooms' => $data['rooms_count'],
                    'total_floors' => $data['floors'],
                    'gallery' => $gallery,
                    'latitude' => $data['lat'],
                    'longitude' => $data['lng'],
                    'status' => $data['status'],
                    'is_featured' => $data['featured'],
                    'created_by' => $userId,
                ]
            );

            // Attach random amenities
            $randomAmenities = collect($amenityModels)->random(rand(4, count($amenityModels)))->pluck('id');
            $hotel->hotelAmenities()->syncWithoutDetaching($randomAmenities);

            // Create rooms for each hotel
            $roomCount = rand(4, 7);
            for ($i = 0; $i < $roomCount; $i++) {
                $basePrice = $data['price'] * (0.7 + ($i * 0.2));
                $totalRoom = rand(5, 30);
                $availableCount = rand(0, $totalRoom);

                // Pick room images
                $roomImgs = array_slice($roomImages, 0, rand(2, count($roomImages)));
                shuffle($roomImgs);

                Room::updateOrCreate(
                    ['hotel_id' => $hotel->id, 'name' => $roomNames[$i % count($roomNames)]],
                    [
                        'total_room' => $totalRoom,
                        'room_type' => $roomTypes[$i % count($roomTypes)],
                        'room_number' => 'N' . (($i + 1) * 100 + rand(1, 20)),
                        'description' => '<p>A comfortable ' . $roomNames[$i % count($roomNames)] . ' with modern amenities and beautiful decor.</p>',
                        'price' => round($basePrice, 2),
                        'discount_price' => rand(0, 1) ? round($basePrice * 0.9, 2) : null,
                        'capacity' => rand(1, 4),
                        'bed_type' => $bedTypes[array_rand($bedTypes)],
                        'bed_count' => rand(1, 3),
                        'room_available_count' => $availableCount,
                        'bathroom_count' => rand(1, 2),
                        'room_size' => rand(20, 80) . ' sqm',
                        'view' => $views[array_rand($views)],
                        'images' => $roomImgs,
                        'is_available' => $availableCount > 0,
                        'sort_order' => $i + 1,
                        'status' => 'active',
                    ]
                );
            }

            // Sync prices from rooms
            $hotel->syncPricesFromRooms();
        }
    }
}
