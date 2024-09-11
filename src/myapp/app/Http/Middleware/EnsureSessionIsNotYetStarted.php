<?php

namespace app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Parking\Session;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionIsNotYetStarted
{
    /**
     * Check if the parking session is already started
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $licensePlate = $request->all()['license_plate'];
        $session = Session::query()->where('license_plate', $licensePlate)->whereNull('ended_at')->first();

        if ($session) {
            return new JsonResponse(['message' => "Your parking session has been already started"], 406);
        }

        return $next($request);
    }
}
