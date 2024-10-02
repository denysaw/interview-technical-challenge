<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Parking\Spot;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpotIsFree
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

        if ($spot->isTaken()) {
            return new JsonResponse(['message' => "Sorry, the spot is taken"], 423);
        }

        return $next($request);
    }
}
