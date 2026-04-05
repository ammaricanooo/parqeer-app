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
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['area_id']);
            $table->dropForeign(['rate_id']);

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('restrict');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('restrict');
            $table->foreign('rate_id')->references('id')->on('rates')->onDelete('restrict');
        });

        Schema::table('rates', function (Blueprint $table) {
            if (!Schema::hasColumn('rates', 'pricing_type')) {
                $table->enum('pricing_type', ['per_hour', 'fixed'])->default('per_hour')->after('vehicle_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['area_id']);
            $table->dropForeign(['rate_id']);

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('rate_id')->references('id')->on('rates')->onDelete('cascade');
        });

        Schema::table('rates', function (Blueprint $table) {
            if (Schema::hasColumn('rates', 'pricing_type')) {
                $table->dropColumn('pricing_type');
            }
        });
    }
};
