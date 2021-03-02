<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class FreightWagonTypePermissionsSeeder extends Seeder
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
            ['name' => 'freight-wagon-type-viewAny'],
            ['name' => 'freight-wagon-type-view'],
            ['name' => 'freight-wagon-type-create'],
            ['name' => 'freight-wagon-type-update'],
            ['name' => 'freight-wagon-type-delete']
        ];
    }
}
