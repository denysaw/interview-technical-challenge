<?php

namespace App\Services;

use Carbon\Carbon;
use App\Enums\SpotTypeEnum;
use App\Models\Parking\Spot;
use App\Models\Parking\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ParkingService
{
    public const VEHICLE_TYPE_CAR = 'car';
    public const VEHICLE_TYPE_VAN = 'van';
    public const VEHICLE_TYPE_MOTO = 'motorcycle';

    /**
     * Retrieves all parking spots' state
     *
     * @return Collection
     */
    public function getSpotsState(): Collection
    {
        return Spot::all();
    }

    /**
     * Initiates new parking session (if there's a free spot(s))
     *
     * @param Spot $spot
     * @param string $vehicleType
     * @param string $licensePlate
     * @return Session|null
     */
    public function startSession(Spot $spot, string $vehicleType, string $licensePlate): ?Session
    {
        $session = null;

        DB::transaction(function () use ($spot, $vehicleType, $licensePlate, &$session) {
            $spots = [];

            // Checking again to ensure it's still free in the transaction
            if ($spot->isTaken()) {
                throw new \Exception("Sorry, the spot is taken");
            }

            switch ($vehicleType) {
                case self::VEHICLE_TYPE_MOTO:
                    if ($spot->type != SpotTypeEnum::Moto) {
                        $freeMotoSpots = Spot::moto()->whereDoesntHave('sessions', function (Builder $qb) {
                            $qb->whereNull('ended_at');
                        })->get();

                        if (count($freeMotoSpots)) {
                            throw new \Exception("There are free moto spots. Please take one of those");
                        }
                    }
                case self::VEHICLE_TYPE_CAR:
                    $spots[] = $spot;
                    break;
                case self::VEHICLE_TYPE_VAN:
                    // Assuming provided spot is the beginning "left" spot for the van
                    $spots = Spot::auto()->whereBetween('id', [$spot->id, $spot->id + 2])
                        ->whereDoesntHave('sessions', function (Builder $qb) {
                            $qb->whereNull('ended_at');
                        })->get();

                    if (count($spots) < 3) {
                        throw new \Exception("Sorry, there's not enough space for your van");
                    }
            }

            // Starting a new session, reserving the spots:
            $session = new Session(['license_plate' => $licensePlate]);
            $session->save();
            $session->spots()->saveMany($spots);
        }, 5);

        return $session;
    }

    /**
     * Ends ongoing parking session
     *
     * @param Session $session
     */
    public function endSession(Session $session): void
    {
        $session->ended_at = Carbon::now();
        $session->save();
    }

    public function clearParkingLot(): void
    {
        Session::query()->whereNull('ended_at')->update(['ended_at' => Carbon::now()]);
    }
}
