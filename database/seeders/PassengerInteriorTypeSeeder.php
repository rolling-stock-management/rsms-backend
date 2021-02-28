<?php

namespace Database\Seeders;

use App\Models\PassengerInteriorType;
use Illuminate\Database\Seeder;

class PassengerInteriorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            PassengerInteriorType::factory()->create(['name' => $data['name'], 'description' => $data['description']]);
        }
    }

    /**
     * Data to be seeded.
     *
     * @return string[][]
     */
    private function data()
    {
        return [
            ['name' => 'Безкупеен', 'description' => null],
            ['name' => 'Бистро', 'description' => null],
            ['name' => 'Купеен', 'description' => null],
            ['name' => 'Кушет', 'description' => null],
            ['name' => 'Смесен', 'description' => null],
            ['name' => 'Спален', 'description' => null],
        ];
    }
}
