<?php

namespace Tests\Unit;

use App\Models\Parking\Spot;
use App\Services\ParkingService;
use Tests\TestCase;

class ParkingServiceTest extends TestCase
{
    private ParkingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ParkingService();
        $this->service->clearParkingLot();
    }

    public function test_park_moto_on_a_moto_spot(): void
    {
        /** @var Spot $spot */
        $spot = Spot::moto()->first();
        $session = $this->service->startSession($spot, 'motorcycle', 'MC-123');

        $this->assertEquals([$spot->id], iterator_to_array($session->spots()->pluck('id')));
    }

    public function test_park_moto_on_a_auto_spot_when_there_are_free_moto_spots(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There are free moto spots. Please take one of those');

        /** @var Spot $spot */
        $spot = Spot::auto()->first();
        $this->service->startSession($spot, 'motorcycle', 'MC-123');
    }

    public function test_park_moto_on_a_auto_spot(): void
    {
        // Occupying all moto spots
        $this->service->startSession(Spot::moto()->where('id', 11)->first(), 'motorcycle', 'MC-123');
        $this->service->startSession(Spot::moto()->where('id', 12)->first(), 'motorcycle', 'MC-124');
        $this->service->startSession(Spot::moto()->where('id', 13)->first(), 'motorcycle', 'MC-125');
        $this->service->startSession(Spot::moto()->where('id', 14)->first(), 'motorcycle', 'MC-126');

        /** @var Spot $spot */
        $spot = Spot::auto()->first();
        $session = $this->service->startSession($spot, 'motorcycle', 'AB-127');

        $this->assertEquals([$spot->id], iterator_to_array($session->spots()->pluck('id')));
    }

    public function test_park_van_on_sufficient_spots(): void
    {
        /** @var Spot $spot */
        $spot = Spot::auto()->first();
        $session = $this->service->startSession($spot, 'van', 'VAN-123');

        $this->assertEquals([1, 2, 3], iterator_to_array($session->spots()->pluck('id')));
    }

    public function test_park_van_on_insufficient_spots(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Sorry, there's not enough space for your van");

        $this->service->startSession(Spot::auto()->where('id', 1)->first(), 'car', 'CAR-123');
        $this->service->startSession(Spot::auto()->where('id', 4)->first(), 'car', 'CAR-124');

        // Parking a van
        $this->service->startSession(Spot::auto()->where('id', 2)->first(), 'van', 'VAN-124');
    }
}
