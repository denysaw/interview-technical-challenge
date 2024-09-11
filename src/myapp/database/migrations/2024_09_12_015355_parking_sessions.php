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
        Schema::create('parking_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('license_plate')->index(); // for the future `by-a-vehicle` aggregations
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable()->index(); // as we use Postgres 8.3+ we no more afraid to index NULLs :)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('parking_sessions');
    }
};
