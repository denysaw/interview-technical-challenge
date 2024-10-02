<?php

namespace app\Http\Middleware;

use App\Models\Parking\Session;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Parking\Spot;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionExists
{
    /**
     * First preliminary check that the requested spot is free
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Spot $spot */
        $spot = $request->attributes->get('spot');
        $licensePlate = $request->all()['license_plate'];

        $session = Session::query()->where('license_plate', $licensePlate)
            ->whereNull('ended_at')
            ->whereHas('spots', function (Builder $qb) use ($spot) {
                $qb->where('id', $spot->id);
            })->first();

        if (!$session) {
            return new JsonResponse(['message' => "Parking session doesn't exist or ended"], 404);
        }

        $request->attributes->add(['session' => $session]);
        return $next($request);
    }
}
