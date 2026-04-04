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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key constraint yang lama (dengan cascade delete)
            $table->dropForeign(['area_id']);

            // Buat foreign key constraint baru tanpa cascade delete
            $table->foreign('area_id')->references('id')->on('areas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kembalikan ke cascade delete jika rollback
            $table->dropForeign(['area_id']);
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }
};
