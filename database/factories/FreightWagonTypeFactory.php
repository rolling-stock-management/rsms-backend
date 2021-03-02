<?php

namespace Database\Factories;

use App\Models\FreightWagonType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FreightWagonTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FreightWagonType::class;

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
        ];
    }
}
