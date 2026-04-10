<?php

namespace Modules\Hotel\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hotel\Enums\HotelReviewEnum;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelReview;

class HotelReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::all();
        $users = User::all();

        if ($hotels->isEmpty()) {
            return;
        }

        if ($users->isEmpty()) {
            // Create a default user if none exists
            $user = User::factory()->create([
                'name' => 'Demo Guest',
                'email' => 'guest@example.com',
            ]);
            $users = collect([$user]);
        }

        $comments = [
            'Excellent stay! The staff was very helpful and the rooms were clean.',
            'Great location, but the breakfast could be improved.',
            'Wonderful experience. Highly recommend the spa services.',
            'The view from the balcony was amazing. Will definitely come back.',
            'Good hotel for business trips. Fast WiFi and quiet rooms.',
            'A bit noisy at night, but otherwise a decent stay.',
            'Pure luxury. The attention to detail is remarkable.',
            'Friendly staff and comfortable beds. Great value for money.',
            'Not what I expected based on the photos. Service was slow.',
            'Perfect for families. The kids loved the pool area.',
        ];

        $replies = [
            'Thank you for your kind words! We hope to see you again soon.',
            'We appreciate your feedback regarding the breakfast. We are working on improving our menu.',
            'We are glad you enjoyed the spa! It is one of our most popular amenities.',
            'We are happy to hear you enjoyed the view. It is truly one of our best features.',
            'Thank you for choosing us for your business stay. Glad the WiFi met your needs.',
        ];

        foreach ($hotels as $hotel) {
            // Create 3-8 reviews per hotel
            $reviewCount = rand(3, 8);

            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $status = $this->getRandomStatus();
                $rating = rand(3, 5); // Mostly positive reviews for better demo

                if ($rating < 3) {
                    $status = HotelReviewEnum::Rejected;
                }

                $review = HotelReview::create([
                    'hotel_id' => $hotel->id,
                    'user_id' => $user->id,
                    'guest_name' => $user->name,
                    'guest_email' => $user->email,
                    'rating' => $rating,
                    'comment' => $comments[array_rand($comments)],
                    'is_recommend' => $rating >= 4,
                    'is_verified' => rand(0, 1) === 1,
                    'status' => $status,
                    'helpful_count' => rand(0, 20),
                ]);

                // Add a reply to some approved reviews
                if ($status === HotelReviewEnum::Approved && rand(0, 1) === 1) {
                    $review->update([
                        'reply' => $replies[array_rand($replies)],
                        'replied_at' => now()->subDays(rand(0, 5)),
                    ]);
                }
            }
        }
    }

    private function getRandomStatus(): HotelReviewEnum
    {
        $statuses = [
            HotelReviewEnum::Approved,
            HotelReviewEnum::Approved,
            HotelReviewEnum::Approved, // Higher weight for approved
            HotelReviewEnum::Pending,
            HotelReviewEnum::Rejected,
        ];

        return $statuses[array_rand($statuses)];
    }
}
