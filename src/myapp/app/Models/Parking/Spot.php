<?php

namespace App\Models\Parking;

use App\Enums\SpotTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Spot extends Model
{
    use HasFactory;

    protected $table = 'parking_spots';

    public $incrementing = false;
    public $timestamps = false; // as there will be no updates in db

    protected $fillable = [
        'id', // primary key would serve us as a Parking Spot #
        'type',
    ];

    protected $casts = [
        'type' => SpotTypeEnum::class, // storing int to save data
    ];

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(Session::class, 'parking_spot_session');
    }

    public function isTaken(): bool
    {
        $lastSession = $this->sessions->last();
        return $lastSession && $lastSession->isOngoing();
    }

    public static function auto(): Builder
    {
        return self::query()->where('type', SpotTypeEnum::Auto);
    }

    public static function moto(): Builder
    {
        return self::query()->where('type', SpotTypeEnum::Moto);
    }
}
