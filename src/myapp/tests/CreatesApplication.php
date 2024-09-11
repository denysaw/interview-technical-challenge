<?php

namespace Tests;

use App\Services\ParkingService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $service = new ParkingService();
        $service->clearParkingLot();

        return $app;
    }
}
