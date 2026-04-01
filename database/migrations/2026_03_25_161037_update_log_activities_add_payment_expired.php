<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum column untuk menambah 'payment_expired'
        DB::statement("ALTER TABLE log_activities MODIFY activity ENUM('entry', 'exit', 'payment', 'payment_expired', 'error')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum asli
        DB::statement("ALTER TABLE log_activities MODIFY activity ENUM('entry', 'exit', 'payment', 'error')");
    }
};
