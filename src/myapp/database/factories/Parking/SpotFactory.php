<?php

namespace database\factories\Parking;

use App\Enums\SpotTypeEnum;
use App\Models\Parking\Spot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SpotFactory extends Factory
{
    protected $model = Spot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(SpotTypeEnum::cases())
        ];
    }

    public function regular(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => SpotTypeEnum::Auto,
            ];
        });
    }

    public function motorcycle(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => SpotTypeEnum::Moto,
            ];
        });
    }
}
