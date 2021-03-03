<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class TractiveUnitPermissionsSeeder extends Seeder
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
            ['name' => 'tractive-unit-viewAny'],
            ['name' => 'tractive-unit-view'],
            ['name' => 'tractive-unit-create'],
            ['name' => 'tractive-unit-update'],
            ['name' => 'tractive-unit-delete']
        ];
    }
}
