<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class TimetablePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            Permission::factory()->create(['name' => $data['name']]);
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
            ['name' => 'timetable-viewAny'],
            ['name' => 'timetable-view'],
            ['name' => 'timetable-create'],
            ['name' => 'timetable-update'],
            ['name' => 'timetable-delete']
        ];
    }
}
