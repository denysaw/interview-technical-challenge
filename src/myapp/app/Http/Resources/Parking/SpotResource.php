<?php

namespace App\Http\Resources\Parking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lastSession = $this->sessions->last();

        return [
            'spot_number' => $this->id,
            'spot_type' => $this->type->name,
            'is_free' => !$lastSession || $lastSession->ended_at,
            'last_session' => $lastSession ?? 'n/a',
        ];
    }
}
