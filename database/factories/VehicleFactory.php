<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['car', 'motorcycle']);

        // Indonesian plate format: [LETTER] [NUMBER] [LETTER] [NUMBER]
        // Example: F 1234 AB or B 9876 XYZ
        $letter1 = $this->faker->randomElement(['A', 'B', 'D', 'E', 'F', 'G', 'H', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z']);
        $number1 = str_pad($this->faker->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT);
        $letter2 = $this->faker->randomElement(array_merge(range('A', 'Z')));
        $letter3 = $this->faker->randomElement(array_merge(range('A', 'Z')));

        $plateNumber = "{$letter1} {$number1} {$letter2}{$letter3}";

        $colors = ['Black', 'White', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Yellow', 'Brown', 'Gold', 'Purple'];

        return [
            'plate_number' => $plateNumber,
            'color' => $this->faker->randomElement($colors),
            'type' => $type,
        ];
    }
}
