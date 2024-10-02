<?php

namespace Database\Seeders;

use App\Models\Parking\Spot;
use Illuminate\Database\Seeder;

class SpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $capacity = config('app.parking_lot.capacity');

        $spots = Spot::factory()->count($capacity['auto'])->regular()->make();
        $spots = $spots->concat(Spot::factory()->count($capacity['moto'])->motorcycle()->make());

        /** @var Spot $spot */
        foreach ($spots as $id => $spot) {
            // As we use `id` as a Parking Spot #, we gotta assign those manually here
            $spot->id = ++$id;
            $spot->save();
        }
    }
}
