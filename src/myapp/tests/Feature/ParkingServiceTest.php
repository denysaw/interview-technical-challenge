<?php

namespace Tests\Feature;

use Tests\TestCase;

class ParkingServiceTest extends TestCase
{

    public function test_park_car_on_moto_spot(): void
    {
        $response = $this->post('/api/parking-spot/11/park', [
            'vehicle_type' => 'car',
            'license_plate' => 'CAR-123'
        ]);

        $response->assertStatus(406);
        $response->assertExactJson(['message' => "You can't park your vehicle on a moto spot"]);
    }

    public function test_park_van_on_moto_spot(): void
    {
        $response = $this->post('/api/parking-spot/11/park', [
            'vehicle_type' => 'van',
            'license_plate' => 'VAN-123'
        ]);

        $response->assertStatus(406);
        $response->assertExactJson(['message' => "You can't park your vehicle on a moto spot"]);
    }

    public function test_start_auto_parking_session(): void
    {
        $response = $this->post('/api/parking-spot/1/park', [
            'vehicle_type' => 'car',
            'license_plate' => 'CAR-123'
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => "Parking session started. Please park on spot(s): 1"
        ]);

        // Check if the spot is taken now
        $response = $this->get('/api/parking-spot/1');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'is_free' => false,
            'license_plate' => 'CAR-123',
            'spot_number' => 1,
            'spot_type' => 'Auto',
        ]);

        sleep(.5);

        // End parking session
        $response = $this->post('/api/parking-spot/1/unpark', [
            'license_plate' => 'CAR-123'
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => "Your parking session successfully ended"
        ]);

        // Check if the spot is free now
        $response = $this->get('/api/parking-spot/1');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'is_free' => true,
            'spot_number' => 1,
            'spot_type' => 'Auto',
        ]);
    }

    public function test_ending_session_with_incorrect_license_plate(): void
    {
        $response = $this->post('/api/parking-spot/1/park', [
            'vehicle_type' => 'car',
            'license_plate' => 'CAR-123'
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => "Parking session started. Please park on spot(s): 1"
        ]);

        $response = $this->post('/api/parking-spot/1/unpark', [
            'license_plate' => 'CAR-555'
        ]);

        $response->assertStatus(404);
        $response->assertExactJson([
            'message' => "Parking session doesn't exist or ended"
        ]);
    }

    public function test_start_same_auto_parking_session_twice(): void
    {
        $response = $this->post('/api/parking-spot/1/park', [
            'vehicle_type' => 'car',
            'license_plate' => 'CAR-123'
        ]);

        $response->assertStatus(200);

        $response = $this->post('/api/parking-spot/1/park', [
            'vehicle_type' => 'car',
            'license_plate' => 'CAR-123'
        ]);

        $response->assertStatus(406);
        $response->assertExactJson([
            'message' => "Your parking session has been already started"
        ]);
    }
}
