<?php

namespace app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Parking\Spot;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpotExists
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
        $spot = Spot::query()->find($request->route()->parameter('id'));

        if (!$spot) {
            return new JsonResponse(['message' => "There's no spot with such number"], 404);
        }

        $request->attributes->add(['spot' => $spot]);
        return $next($request);
    }
}
