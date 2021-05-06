<?php

namespace Database\Factories;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\RepairWorkshop;
use App\Models\Status;
use App\Models\TractiveUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class TractiveUnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TractiveUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->phoneNumber,
            'max_speed' => $this->faker->numberBetween($min = 100, $max = 200),
            'power_output' => $this->faker->numberBetween($min = 2000, $max = 6000),
            'tractive_effort' => $this->faker->numberBetween($min = 100, $max = 500),
            'weight' => $this->faker->numberBetween($min = 80, $max = 150),
            'axle_arrangement' => $this->faker->name,
            'length' => $this->faker->numberBetween($min = 10, $max = 30),
            'brake_marking' => $this->faker->name,
            'owner_id' => Owner::factory(),
            'status_id' => Status::factory(),
            'repair_date' => $this->faker->date(),
            'repair_valid_until' => $this->faker->date(),
            'repair_workshop_id' => RepairWorkshop::factory(),
            'depot_id' => Depot::factory(),
            'other_info' => $this->faker->text
        ];
    }
}
