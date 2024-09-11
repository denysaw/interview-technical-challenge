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
        Schema::create('parking_spot_session', function (Blueprint $table) {
            $table->unsignedInteger('spot_id');
            $table->ulid('session_id');
            $table->primary(['spot_id', 'session_id']);

            $table->foreign('spot_id')->references('id')
                ->on('parking_spots')->onDelete('cascade');

            $table->foreign('session_id')->references('id')
                ->on('parking_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('parking_spot_session');
    }
};
