<?php

namespace Database\Factories;

use App\Models\Timetable;
use App\Models\Train;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Train::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->numberBetween(10000, 99999),
            'route' => $this->faker->address,
            'note' => $this->faker->text,
            'timetable_id' => Timetable::factory()
        ];
    }
}
