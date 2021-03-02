<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            Owner::factory()->create(['name' => $data['name'], 'note' => $data['note']]);
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
            ['name' => 'BDZPP', 'note' => '"БДЖ - Товарни превози" ЕООД'],
            ['name' => 'BDZTP', 'note' => '"БДЖ - Пътнически превози" ЕООД'],
            ['name' => 'DMVRC', 'note' => '"ДМВ Карго Рейл" ЕООД '],
            ['name' => 'OBB', 'note' => 'Австрийски железници / "Рейл Карго Кериър България" ЕООД '],
            ['name' => 'PIMK', 'note' => '"ПИМК Рейл" ЕАД '],
            ['name' => 'TPPBD', 'note' => '"ТБД - Товарни превози" ЕАД '],
            ['name' => 'WASCO', 'note' => 'Wascosa'],
        ];
    }
}
