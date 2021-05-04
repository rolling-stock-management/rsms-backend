<?php

namespace Database\Seeders;

use App\Models\Train;
use Illuminate\Database\Seeder;

class TrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            Train::factory()->create([
                'number' => $data['number'],
                'route' => $data['route'],
                'note' => $data['note'],
                'timetable_id' => $data['timetable_id']
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
            ['number' => '10242', 'route' => 'Димитровград - Пловдив', 'note' => '', 'timetable_id' => 2],
            ['number' => '10261', 'route' => 'Пловдив -  Димитровград', 'note' => '', 'timetable_id' => 2],
            ['number' => '82202', 'route' => 'Пловдив - Карлово ', 'note' => '', 'timetable_id' => 2],
            ['number' => '82203', 'route' => 'Карлово - Пловдив', 'note' => '', 'timetable_id' => 2],
            ['number' => '1611', 'route' => 'София - Свиленград', 'note' => '', 'timetable_id' => 2],
            ['number' => '1614', 'route' => 'Свиленград - София', 'note' => '', 'timetable_id' => 2],
            ['number' => '464', 'route' => 'Димитровград - Горна Оряховица', 'note' => 'лок44', 'timetable_id' => 2],
            ['number' => '5683', 'route' => 'София - Благоевград', 'note' => '', 'timetable_id' => 2],
            ['number' => '462', 'route' => 'Горна Оряховица - Русе', 'note' => '', 'timetable_id' => 2],
            ['number' => '2655', 'route' => 'Враца - Варна', 'note' => '', 'timetable_id' => 2],
            ['number' => '40123', 'route' => 'Горна Оряховица - Стара Загора', 'note' => '', 'timetable_id' => 2],
            ['number' => '30140', 'route' => 'Бургас - Карлово', 'note' => '', 'timetable_id' => 2],
            ['number' => '3602', 'route' => 'Бургас - София през Карлово', 'note' => '', 'timetable_id' => 2],
            ['number' => '8641', 'route' => 'София - Стара Загора', 'note' => '', 'timetable_id' => 2],
            ['number' => '30114', 'route' => 'Карлово - София', 'note' => 'лок44', 'timetable_id' => 2],
            ['number' => '2601', 'route' => 'София - Варна', 'note' => '', 'timetable_id' => 2],
            ['number' => '4641', 'route' => 'Русе - Стара Загора', 'note' => '', 'timetable_id' => 2],
            ['number' => '40154', 'route' => 'Момчилград - Димитровгард', 'note' => '', 'timetable_id' => 2],
        ];
    }
}
