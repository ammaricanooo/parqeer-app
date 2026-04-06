<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Area;
use App\Models\Rate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrentTransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startTime = Carbon::create(2026, 4, 6, 11, 35, 0);
        $endTime = $startTime->copy()->addHour();

        $vehicleData = [
            ['type' => 'car', 'count' => 10, 'rate' => 7000, 'area_name' => 'G'],
            ['type' => 'motorcycle', 'count' => 15, 'rate' => 5000, 'area_name' => 'm'],
            ['type' => 'truck', 'count' => 20, 'rate' => 10000, 'area_name' => 'k'],
            ['type' => 'bus', 'count' => 20, 'rate' => 10000, 'area_name' => 'l'],
        ];

        // Get or create a user (assume first user or create)
        $user = User::first() ?? User::factory()->create();

        foreach ($vehicleData as $data) {
            $area = Area::where('name', $data['area_name'])->first();
            if (!$area) {
                $area = Area::factory()->create(['name' => $data['area_name'], 'capacity' => 50, 'occupied' => 0]);
            }

            $rate = Rate::where('area_id', $area->id)
                ->where('vehicle_type', $data['type'])
                ->where('amount', $data['rate'])
                ->first();
            if (!$rate) {
                $rate = Rate::create([
                    'area_id' => $area->id,
                    'vehicle_type' => $data['type'],
                    'amount' => $data['rate'],
                    'pricing_type' => 'per_hour',
                ]);
            }

            for ($i = 0; $i < $data['count']; $i++) {
                $vehicle = Vehicle::factory()->create([
                    'type' => $data['type'],
                ]);

                Transaction::create([
                    'vehicle_id' => $vehicle->id,
                    'area_id' => $area->id,
                    'rate_id' => $rate->id,
                    'user_id' => $user->id,
                    'plate_number' => $vehicle->plate_number,
                    'entry_time' => $startTime,
                    'exit_time' => $endTime,
                    'amount' => $data['rate'],
                    'status' => 'in',
                ]);
            }
        }
    }
}
