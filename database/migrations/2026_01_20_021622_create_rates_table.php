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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck', 'bus']); // Beda harga per tipe
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            // Satu area bisa punya multiple rates (1 untuk car, 1 untuk motorcycle)
            $table->unique(['area_id', 'vehicle_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
