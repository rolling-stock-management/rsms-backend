<?php

namespace Database\Factories;

use App\Models\RepairWorkshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairWorkshopFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RepairWorkshop::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'abbreviation' => $this->faker->name,
            'note' => $this->faker->text,
        ];
    }
}
