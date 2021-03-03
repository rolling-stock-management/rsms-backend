<?php

namespace Database\Seeders;

use App\Models\RepairWorkshop;
use Illuminate\Database\Seeder;

class RepairWorkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            RepairWorkshop::factory()->create([
                'name' => $data['name'],
                'abbreviation' => $data['abbreviation'],
                'note' => $data['note']
            ]);
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
            ['name' => 'Австрия', 'abbreviation' => 'WS', 'note' => null],
            ['name' => '„Вагонен завод – Интерком“ АД, Дряново', 'abbreviation' => 'DV', 'note' => null],
            ['name' => 'Германия', 'abbreviation' => 'MMAL', 'note' => null],
            ['name' => 'Горна Оряховица', 'abbreviation' => 'GO', 'note' => null],
            ['name' => '„Коловаг“ АД, Септември', 'abbreviation' => 'KWG', 'note' => null],
            ['name' => 'Левски', 'abbreviation' => 'L', 'note' => null],
            ['name' => 'Надежда', 'abbreviation' => 'ND', 'note' => null],
            ['name' => 'Пловдив', 'abbreviation' => 'PO', 'note' => null],
            ['name' => 'Румъния', 'abbreviation' => 'RO', 'note' => null],
            ['name' => 'Русе', 'abbreviation' => 'RR', 'note' => null],
            ['name' => 'Русе разпределителна', 'abbreviation' => 'R', 'note' => null],
            ['name' => 'Септември', 'abbreviation' => 'SP', 'note' => null],
            ['name' => '„Тракция“ АД', 'abbreviation' => 'ЖПТ', 'note' => null],
            ['name' => 'Хан Крум', 'abbreviation' => 'HCR', 'note' => null],
            ['name' => 'Чехия/Словакия', 'abbreviation' => 'PR', 'note' => null],
        ];
    }
}
