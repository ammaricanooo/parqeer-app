<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->createMany([
            [
            'name'     => 'Ammar Abdul Malik',
            'username' => 'ammar',
            'password' => 'password',
            'role'     => 'admin',
            'status'   => 'active',
        ], [
            'name'     => 'Jawa Berkelas',
            'username' => 'jawa',
            'password' => 'password',
            'role'     => 'attendant',
            'status'   => 'active',
        ]
        ]);

        Area::factory()->createMany([
            [
            'name'     => 'A-1',
            'capacity' => 20,
            'occupied' => 0
        ], [
            'name'     => 'B-1',
            'capacity' => 20,
            'occupied' => 0
        ]
        ]);
    }
}
