<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hotel\Models\RoomPolicies;

class RoomPoliciesFactory extends Factory
{
    protected $model = RoomPolicies::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Check-in / Check-out',
                'No Smoking',
                'Pet Policy',
                'Cancellation',
                'Children & Extra Beds',
                'Damage Deposit',
            ]),
            'icon' => fake()->randomElement(['Clock', 'CigaretteOff', 'PawPrint', 'XCircle', 'Baby', 'ShieldCheck']),
            'description' => fake()->sentence(12),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
