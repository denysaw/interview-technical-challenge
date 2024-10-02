<?php

namespace App\Providers;

use App\Services\ParkingService;
use Illuminate\Support\ServiceProvider;

class ParkingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ParkingService::class, fn() => new ParkingService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
