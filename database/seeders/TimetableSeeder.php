<?php

namespace Database\Seeders;

use App\Models\Timetable;
use Illuminate\Database\Seeder;

class TimetableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            Timetable::factory()->create(['start_date' => $data['start_date'], 'end_date' => $data['end_date']]);
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
            ['start_date' => '15-12-2019', 'end_date' => '14-12-2020'],
            ['start_date' => '15-12-2019', 'end_date' => '14-12-2020'],
        ];
    }
}
