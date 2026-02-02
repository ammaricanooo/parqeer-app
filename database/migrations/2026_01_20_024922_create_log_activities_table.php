<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // Link ke transaksi
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade'); // Link ke kendaraan
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Bisa null jika auto-detect
            $table->enum('activity', ['entry', 'exit', 'payment', 'error']); // Jenis aktivitas
            $table->string('plate_number', 20); // Untuk tracking
            $table->string('vehicle_color', 30)->nullable(); // Dari deteksi
            $table->text('description')->nullable(); // Detail aktivitas
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('vehicle_id');
            $table->index('activity');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activities');
    }
};
