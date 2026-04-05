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
        Schema::table('rates', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropUnique(['area_id', 'vehicle_type']);
            $table->unique('area_id');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropUnique(['area_id']);
            $table->unique(['area_id', 'vehicle_type']);
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }
};
