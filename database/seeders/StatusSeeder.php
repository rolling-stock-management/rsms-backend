<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            Status::factory()->create(['name' => $data['name']]);
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
            ['name' => 'Бракуван'],
            ['name' => 'В движение'],
            ['name' => 'В ремонт'],
            ['name' => 'Изтекла ревизия'],
            ['name' => 'Нарязан'],
            ['name' => 'Неизвестен'],
            ['name' => 'Спрян']
        ];
    }
}
