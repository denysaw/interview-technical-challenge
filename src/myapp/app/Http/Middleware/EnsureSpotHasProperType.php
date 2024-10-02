<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\SpotTypeEnum;
use Illuminate\Http\Request;
use App\Models\Parking\Spot;
use App\Services\ParkingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpotHasProperType
{
    /**
     * Check if the requested spot has a corresponding type
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Spot $spot */
        $spot = $request->attributes->get('spot');
        $vehicleType = $request->all()['vehicle_type'];

        if ($spot->type === SpotTypeEnum::Moto && $vehicleType !== ParkingService::VEHICLE_TYPE_MOTO) {
            return new JsonResponse(['message' => "You can't park your vehicle on a moto spot"], 406);
        }

        return $next($request);
    }
}
