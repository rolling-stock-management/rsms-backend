<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class UserPermissionsSeeder extends Seeder
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
            ['name' => 'user-viewAny'],
            ['name' => 'user-view'],
            ['name' => 'user-create'],
            ['name' => 'user-update'],
            ['name' => 'user-delete']
        ];
    }
}
