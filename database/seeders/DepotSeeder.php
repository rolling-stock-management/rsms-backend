<?php

namespace Database\Seeders;

use App\Models\Depot;
use Illuminate\Database\Seeder;

class DepotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            Depot::factory()->create(['name' => $data['name'], 'note' => $data['note']]);
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
            ['name' => 'Бургас', 'note' => null],
            ['name' => 'Варна', 'note' => null],
            ['name' => 'Горна Оряховица', 'note' => null],
            ['name' => 'Дупница', 'note' => 'Адрес: гр. Дупница, ул. Аракчийски мост'],
            ['name' => 'Карнобат', 'note' => null],
            ['name' => 'Мездра', 'note' => null],
            ['name' => 'Перник', 'note' => null],
            ['name' => 'Пловдив', 'note' => null],
            ['name' => 'Подуяне', 'note' => null],
            ['name' => 'Русе', 'note' => null],
            ['name' => 'Перник', 'note' => null],
            ['name' => 'София', 'note' => null],
            ['name' => 'Синдел', 'note' => 'Адрес: гара Синдел, Вагоноремонтен цех Синдел'],
            ['name' => 'Стара Загора', 'note' => null],
        ];
    }
}
