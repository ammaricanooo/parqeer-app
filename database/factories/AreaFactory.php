<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $areaCounter = 0;
        $rows = ['A', 'B', 'C', 'D'];
        $row = $rows[$areaCounter % count($rows)];
        $number = floor($areaCounter / count($rows)) + 1;

        $areaCounter++;

        return [
            'name' => "{$row}-{$number}",
            'capacity' => $this->faker->randomElement([20, 25, 30, 40, 50]),
            'occupied' => 0,
        ];
    }
}
