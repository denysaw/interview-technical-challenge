<?php

namespace App\Http\Controllers;

use App\Models\Parking\Spot;
use Illuminate\Http\Request;
use App\Models\Parking\Session;
use App\Services\ParkingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Parking\SpotResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ParkingController extends Controller
{
    public const SPOTS_STATE_CACHE_KEY = 'spots_state';

    public function __construct(
        protected ParkingService $parkingService,
    ) {}

    /**
     * @return ResourceCollection
     */
    public function getSpotsState(): ResourceCollection
    {
        $cached = Cache::get(self::SPOTS_STATE_CACHE_KEY);

        if ($cached) {
            return $cached;
        }

        $result = SpotResource::collection($this->parkingService->getSpotsState());
        Cache::set(self::SPOTS_STATE_CACHE_KEY, $result);
        return $result;
    }

    public function getSingleSpotInfo(Request $request): SpotResource
    {
        return SpotResource::make($request->attributes->get('spot'));
    }

    public function getSpotSessions(Request $request): JsonResponse
    {
        /** @var Spot $spot */
        $spot = $request->attributes->get('spot');
        return new JsonResponse($spot->sessions->take(100));
    }

    /**
     * @param int $spotId
     * @param Request $request
     * @return JsonResponse
     */
    public function startSession(int $spotId, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'vehicle_type' => 'required|string|max:20|in:car,van,motorcycle',
                'license_plate' => 'required|string|max:10',
            ]);
        } catch (\Exception $e) {
            // TODO: Replace with a correct error messages
            return new JsonResponse([
                'message' => "Invalid request, body args: vehicle_type (car|van|motorcycle), license_plate"
            ], 400);
        }

        $body = $request->all();
        $vehicleType = $body['vehicle_type'];
        $licensePlate = $body['license_plate'];

        /** @var Spot $spot */
        $spot = $request->attributes->get('spot');

        try {
            $session = $this->parkingService->startSession($spot, $vehicleType, $licensePlate);
            if (!$session) {
                // Handle the deadlock case:
                return new JsonResponse(['message' => 'Server error'], 500);
            }

            $spotIds = join(', ', iterator_to_array($session->spots()->pluck('id')));
            Cache::clear();

            return new JsonResponse([
                'success' => true,
                'message' => "Parking session started. Please park on spot(s): $spotIds"
            ]);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 409);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function stopSession(Request $request): JsonResponse
    {
        /** @var Session $session */
        $session = $request->attributes->get('session');

        try {
            $this->parkingService->endSession($session);
            Cache::clear();
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse([
            'success' => true,
            'message' => "Your parking session successfully ended"
        ]);
    }
}
