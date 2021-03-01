<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PassengerWagonTypePermissionsSeeder extends Seeder
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
    private
    function data()
    {
        return [
            ['name' => 'passenger-wagon-type-viewAny'],
            ['name' => 'passenger-wagon-type-view'],
            ['name' => 'passenger-wagon-type-create'],
            ['name' => 'passenger-wagon-type-update'],
            ['name' => 'passenger-wagon-type-delete']
        ];
    }
}
