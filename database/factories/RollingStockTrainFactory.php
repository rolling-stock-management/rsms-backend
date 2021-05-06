<?php

namespace Database\Factories;

use App\Models\RollingStockTrain;
use App\Models\Train;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RollingStockTrainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RollingStockTrain::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'position' => $this->faker->numberBetween(1, 10),
            'comment' => $this->faker->sentence,
            'train_id' => Train::factory(),
            'user_id' => User::factory()
        ];
    }
}
