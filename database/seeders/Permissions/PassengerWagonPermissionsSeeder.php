<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PassengerWagonPermissionsSeeder extends Seeder
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
            ['name' => 'passenger-wagon-viewAny'],
            ['name' => 'passenger-wagon-view'],
            ['name' => 'passenger-wagon-create'],
            ['name' => 'passenger-wagon-update'],
            ['name' => 'passenger-wagon-delete']
        ];
    }
}
