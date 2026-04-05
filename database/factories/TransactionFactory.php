<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\Area;
use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entryTime = Carbon::instance($this->faker->dateTimeBetween('2026-03-20', '2026-04-05'));
        $durationMinutes = $this->faker->randomElement([30, 60, 90, 120, 180, 240, 360, 480, 720, 1440]);
        $exitTime = $entryTime->copy()->addMinutes($durationMinutes);

        // Get or create relationships
        $vehicle = Vehicle::factory()->create();
        $user = User::whereIn('role', ['attendant', 'admin'])->inRandomOrder()->first()
            ?? User::factory()->create(['role' => 'attendant']);
        $area = Area::inRandomOrder()->first() ?? Area::factory()->create();

        // Get rates for the selected area
        $rate = $area->rates()->first() ?? Rate::factory()->create(['area_id' => $area->id]);

        // Calculate amount based on duration and rate
        $hours = ceil($durationMinutes / 60);
        $amount = $hours * $rate->amount;

        $status = $this->faker->randomElement(['in', 'paid', 'out']);
        $paidAmount = null;
        $change = null;
        $paidAt = null;

        if ($status === 'paid' || $status === 'out') {
            $paidAmount = $amount;
            $change = 0;
            $paidAt = $exitTime->copy()->addMinutes($this->faker->numberBetween(1, 5));
        }

        return [
            'vehicle_id' => $vehicle->id,
            'user_id' => $user->id,
            'area_id' => $area->id,
            'rate_id' => $rate->id,
            'plate_number' => $vehicle->plate_number,
            'vehicle_color' => $vehicle->color,
            'entry_time' => $entryTime,
            'exit_time' => $status === 'out' || $status === 'paid' ? $exitTime : null,
            'duration_minutes' => $status === 'out' || $status === 'paid' ? $durationMinutes : null,
            'amount' => $status === 'out' || $status === 'paid' ? $amount : null,
            'status' => $status,
            'paid_amount' => $paidAmount,
            'change' => $change,
            'paid_at' => $paidAt,
            'payment_method' => $status === 'out' || $status === 'paid' ? 'cash' : null,
        ];
    }
}
