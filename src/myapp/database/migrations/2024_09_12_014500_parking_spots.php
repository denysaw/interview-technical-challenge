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
        Schema::create('parking_spots', function (Blueprint $table) {
            /*
             * As mentioned in the corresponding model, we've disabled
             * auto-increment to use `id` as a Parking Spot #
             */
            $table->unsignedInteger('id')->primary();
            $table->unsignedSmallInteger('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('parking_spots');
    }
};
