<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area_id' => Area::factory(),
            'vehicle_type' => $this->faker->randomElement(['car', 'motorcycle']),
            'amount' => $this->faker->randomElement([2000, 3000, 5000, 7000]), // Hourly rates in IDR
            'pricing_type' => 'per_hour',
        ];
    }
}
