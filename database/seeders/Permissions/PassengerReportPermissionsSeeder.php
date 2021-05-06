<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PassengerReportPermissionsSeeder extends Seeder
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
            ['name' => 'passenger-report-viewAny'],
            ['name' => 'passenger-report-view'],
            ['name' => 'passenger-report-update'],
            ['name' => 'passenger-report-delete']
        ];
    }
}
