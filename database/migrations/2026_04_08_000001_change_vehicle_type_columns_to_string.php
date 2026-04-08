<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY type VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE rates MODIFY vehicle_type VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY type ENUM('car','motorcycle','truck','bus') NOT NULL");
        DB::statement("ALTER TABLE rates MODIFY vehicle_type ENUM('car','motorcycle','truck','bus') NOT NULL");
    }
};
