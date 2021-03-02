<?php

namespace Database\Seeders;

use App\Models\FreightWagonType;
use Illuminate\Database\Seeder;

class FreightWagonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            FreightWagonType::factory()->create(['name' => $data['name'], 'description' => $data['description']]);
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
            ['name' => 'E', 'description' => 'Обикновени отворени вагони с високи странични капаци'],
            ['name' => 'F', 'description' => 'Специални отворени вагони с високи странични капаци (седловидни, хопери)'],
            ['name' => 'G', 'description' => 'Обикновени покрити вагони'],
            ['name' => 'H', 'description' => 'Специални покрити вагони (с раздвижени стени)'],
            ['name' => 'I', 'description' => 'Вагони с контролирана температура'],
            ['name' => 'K', 'description' => 'Обикновени двуосни вагони с равен под и отделни колооси'],
            ['name' => 'L', 'description' => 'Специални двуосни вагони с равен под и отделни колооси'],
            ['name' => 'O', 'description' => 'Вагони със смесен под, отворени, с високи капаци'],
            ['name' => 'R', 'description' => 'Обикновени вагони с равен под на талиги'],
            ['name' => 'S', 'description' => 'Специални вагони с равен под на талиги'],
            ['name' => 'T', 'description' => 'Вагони с отварящ се покрив'],
            ['name' => 'U', 'description' => 'Спезциални вагони (зърновози)'],
            ['name' => 'Z', 'description' => 'Цистерни'],
        ];
    }
}
