<?php

namespace Database\Seeders;

use App\Models\RepairType;
use Illuminate\Database\Seeder;

class RepairTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            RepairType::factory()->create(['name' => $data['name'], 'description' => $data['description']]);
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
            ['name' => 'ГПР', 'description' => 'Голям периодичен ремонт'],
            ['name' => 'КР', 'description' => 'Капитален ремонт'],
            ['name' => 'КП', 'description' => 'Контролен преглед'],
            ['name' => 'МПР', 'description' => 'Малък периодичен ремонт'],
            ['name' => 'ПДР', 'description' => 'Подемен деповски ремонт'],
            ['name' => 'РН', 'description' => 'Ремонт по необходимост'],
            ['name' => 'СР', 'description' => 'Среден ремонт'],
            ['name' => 'ТП', 'description' => 'Технически предглед'],
        ];
    }
}
