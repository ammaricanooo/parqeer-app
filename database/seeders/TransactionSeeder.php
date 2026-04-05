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

class TransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create areas if not exist
        $areas = Area::whereIn('name', ['A-1', 'B-1'])->get();
        if ($areas->isEmpty()) {
            $areas = Area::factory(2)->create();
        }

        // Create rates for each area and vehicle type
        foreach ($areas as $area) {
            foreach (['car', 'motorcycle'] as $type) {
                Rate::firstOrCreate(
                    ['area_id' => $area->id, 'vehicle_type' => $type],
                    [
                        'amount' => $type === 'car' ? 15000 : 5000, // IDR per hour
                        'pricing_type' => 'per_hour'
                    ]
                );
            }
        }

        // Get users (attendants)
        $users = User::where('role', 'attendant')->get();
        if ($users->isEmpty()) {
            $users = User::factory(3)->create(['role' => 'attendant']);
        }

        // Create diverse vehicle data
        $plateNumbers = [
            'F 1234 AB',
            'F 5678 CD',
            'B 9876 EF',
            'D 4321 GH',
            'L 3333 IJ',
            'A 1111 KL',
            'B 2222 MN',
            'D 3333 OP',
            'E 4444 QR',
            'F 5555 ST',
            'G 6666 UV',
            'H 7777 WX',
            'K 8888 YZ',
            'L 9999 AA',
            'M 1357 BB',
            'N 2468 CC',
            'P 1357 DD',
            'R 2468 EE',
            'S 1111 FF',
            'T 2222 GG',
        ];

        $colors = ['Black', 'White', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Yellow', 'Brown', 'Gold'];
        $types = ['car', 'motorcycle'];

        // Create vehicles or use existing
        $vehicles = [];
        foreach ($plateNumbers as $plate) {
            $type = rand(1, 2) === 1 ? 'car' : 'motorcycle';
            $vehicles[] = Vehicle::firstOrCreate(
                ['plate_number' => $plate],
                [
                    'color' => $colors[array_rand($colors)],
                    'type' => $type
                ]
            );
        }

        // Generate transactions from 2026-03-20 to 2026-04-05
        $startDate = Carbon::create(2026, 3, 20, 6, 0, 0);
        $endDate = Carbon::create(2026, 4, 5, 23, 59, 59);
        $transactionCount = 0;

        // Create roughly 20-30 transactions per day
        for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $transactionsPerDay = rand(20, 35);

            for ($i = 0; $i < $transactionsPerDay; $i++) {
                // Random time between 6 AM and 11 PM
                $entryHour = rand(6, 23);
                $entryMinute = rand(0, 59);
                $entryTime = $date->copy()->setHour($entryHour)->setMinute($entryMinute);

                // Random parking duration: 30 mins to 8 hours
                $durationMinutes = [30, 45, 60, 90, 120, 180, 240, 360, 480][array_rand([30, 45, 60, 90, 120, 180, 240, 360, 480])];
                $exitTime = $entryTime->copy()->addMinutes($durationMinutes);

                // Skip if exit time goes beyond the date range
                if ($exitTime->greaterThan($endDate)) {
                    continue;
                }

                // Random vehicle and area
                $vehicle = $vehicles[array_rand($vehicles)];
                $area = $areas->random();
                $user = $users->random();
                $rate = $area->rates()->where('vehicle_type', $vehicle->type)->first();

                if (!$rate) {
                    continue;
                }

                // Calculate amount
                $hours = ceil($durationMinutes / 60);
                $amount = $hours * $rate->amount;

                // All transactions are "out" (completed)
                $status = 'out';

                $paidAt = $exitTime->copy()->addMinutes(rand(1, 5));
                $paidAmount = $amount;
                $change = 0;

                Transaction::create([
                    'vehicle_id' => $vehicle->id,
                    'user_id' => $user->id,
                    'area_id' => $area->id,
                    'rate_id' => $rate->id,
                    'plate_number' => $vehicle->plate_number,
                    'vehicle_color' => $vehicle->color,
                    'entry_time' => $entryTime,
                    'exit_time' => $exitTime,
                    'duration_minutes' => $durationMinutes,
                    'amount' => $amount,
                    'status' => $status,
                    'paid_amount' => $paidAmount,
                    'change' => $change,
                    'paid_at' => $paidAt,
                    'payment_method' => 'cash',
                ]);

                $transactionCount++;
            }
        }

        $this->command->info("Generated {$transactionCount} transactions from 2026-03-20 to 2026-04-05");
    }
}
