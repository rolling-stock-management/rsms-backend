<?php

namespace Database\Factories;

use App\Models\PassengerReport;
use App\Models\PassengerWagon;
use App\Models\Train;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassengerReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PassengerReport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'date' => $this->faker->date(),
            'problem_description' => $this->faker->sentence,
            'wagon_number' => $this->faker->numberBetween(1, 10),
            'train_id'=> Train::factory()->create(),
            'wagon_id'=> PassengerWagon::factory()->create()
        ];
    }
}
