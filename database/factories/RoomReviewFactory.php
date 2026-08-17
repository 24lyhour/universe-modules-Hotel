<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\RoomReview;

class RoomReviewFactory extends Factory
{
    protected $model = RoomReview::class;

    public function definition(): array
    {
        $rating = fake()->numberBetween(3, 5);

        return [
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'rating' => $rating,
            'comment' => fake()->sentence(fake()->numberBetween(8, 20)),
            'is_recommend' => $rating >= 4,
            'is_verified' => fake()->boolean(70),
            'is_active' => true,
            'helpful_count' => fake()->numberBetween(0, 25),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
