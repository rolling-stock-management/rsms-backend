<?php

namespace Database\Factories;

use App\Models\PassengerInteriorType;
use App\Models\PassengerWagonType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassengerWagonTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PassengerWagonType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'interior_type_id' => PassengerInteriorType::factory(),
            'repair_valid_for' => $this->faker->numberBetween(1, 10)
        ];
    }
}
