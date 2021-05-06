<?php

namespace Database\Factories;

use App\Models\Repair;
use App\Models\RepairType;
use App\Models\RepairWorkshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Repair::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'short_description' => $this->faker->name,
            'description' => $this->faker->text,
            'type_id' => RepairType::factory(),
            'workshop_id' => RepairWorkshop::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}
