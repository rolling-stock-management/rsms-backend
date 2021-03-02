<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class OwnerPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public
    function run()
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
            ['name' => 'owner-viewAny'],
            ['name' => 'owner-view'],
            ['name' => 'owner-create'],
            ['name' => 'owner-update'],
            ['name' => 'owner-delete']
        ];
    }
}
