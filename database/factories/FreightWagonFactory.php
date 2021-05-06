<?php

namespace Database\Factories;

use App\Models\Depot;
use App\Models\FreightWagon;
use App\Models\FreightWagonType;
use App\Models\Owner;
use App\Models\RepairWorkshop;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class FreightWagonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FreightWagon::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $number = 100000000000 + $this->faker->numberBetween(10000000);
        return [
            'number' => $number,
            'type_id' => FreightWagonType::factory(),
            'letter_marking' => $this->faker->name,
            'tare' => $this->faker->numberBetween($min = 40, $max = 75),
            'weight_capacity' => $this->faker->numberBetween($min = 20, $max = 100),
            'length_capacity' => $this->faker->numberBetween($min = 20, $max = 100),
            'volume_capacity' => $this->faker->numberBetween($min = 20, $max = 100),
            'area_capacity' => $this->faker->numberBetween($min = 20, $max = 100),
            'max_speed' => $this->faker->numberBetween($min = 100, $max = 120),
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
