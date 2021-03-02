<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class RepairTypePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
{
    foreach($this->data() as $data)
    {
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
        ['name' => 'repair-type-viewAny'],
        ['name' => 'repair-type-view'],
        ['name' => 'repair-type-create'],
        ['name' => 'repair-type-update'],
        ['name' => 'repair-type-delete']
    ];
}
}
