<?php

namespace App\Models\Parking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Session extends Model
{
    use HasUlids; // let's assume some future scalability

    protected $table = 'parking_sessions';
    public $timestamps = false;

    protected $fillable = [
        'license_plate',
        'started_at',
        'ended_at',
    ];

    protected $hidden = [
        'pivot'
    ];

    protected $attributes = [
        'started_at' => 'NOW',
    ];

    protected $casts = [
        'license_plate' => 'string',
        'started_at' => 'immutable_datetime',
        'ended_at' => 'immutable_datetime',
    ];

    public function spots(): BelongsToMany
    {
        return $this->BelongsToMany(Spot::class, 'parking_spot_session');
    }

    public function isOngoing(): bool
    {
        return is_null($this->ended_at);
    }
}
