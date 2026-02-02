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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade'); // Link ke kendaraan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->foreignId('rate_id')->constrained('rates')->onDelete('cascade');
            $table->string('plate_number', 20); // Duplikasi untuk deteksi ANPR
            $table->string('vehicle_color', 30)->nullable(); // Dari deteksi warna
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Durasi parkir dalam menit
            $table->decimal('amount', 10, 2)->nullable(); // Null saat entry, diisi saat exit/payment
            $table->enum('status', ['parked', 'paid', 'pending_payment'])->default('parked'); // Status pembayaran
            $table->timestamps();

            $table->index('vehicle_id');
            $table->index('user_id');
            $table->index('area_id');
            $table->index('entry_time');
            $table->index('plate_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
