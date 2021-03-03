<?php

namespace Database\Factories;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\PassengerWagon;
use App\Models\PassengerWagonType;
use App\Models\RepairWorkshop;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassengerWagonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PassengerWagon::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $number = $this->faker->numberBetween(10000000);
        $wagonType = substr($number, 4, 2).'-'.substr($number, 6, 2);
        return [
            'number' => $number,
            'type_id' => PassengerWagonType::factory()->create(['name' => $wagonType]),
            'letter_marking' => $this->faker->name,
            'tare' => $this->faker->numberBetween($min = 40, $max = 75),
            'total_weight' => $this->faker->numberBetween($min = 50, $max = 100),
            'seats_count' => $this->faker->numberBetween($min = 15, $max = 100),
            'max_speed' => $this->faker->numberBetween($min = 100, $max = 200),
            'length' => $this->faker->numberBetween($min = 10, $max = 30),
            'brake_marking' => $this->faker->name,
            'owner_id' => Owner::factory()->create(),
            'status_id' => Status::factory()->create(),
            'repair_date' => $this->faker->date(),
            'repair_valid_until' => $this->faker->date(),
            'repair_workshop_id' => RepairWorkshop::factory()->create(),
            'depot_id' => Depot::factory()->create(),
            'other_info' => $this->faker->text
        ];
    }
}
