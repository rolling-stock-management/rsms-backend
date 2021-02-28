<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PassengerInteriorTypePermissionsSeeder extends Seeder
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
            ['name' => 'passenger-interior-type-viewAny'],
            ['name' => 'passenger-interior-type-view'],
            ['name' => 'passenger-interior-type-create'],
            ['name' => 'passenger-interior-type-update'],
            ['name' => 'passenger-interior-type-delete']
        ];
    }
}
