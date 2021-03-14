<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class FreightWagonPermissionsSeeder extends Seeder
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
            ['name' => 'freight-wagon-viewAny'],
            ['name' => 'freight-wagon-view'],
            ['name' => 'freight-wagon-create'],
            ['name' => 'freight-wagon-update'],
            ['name' => 'freight-wagon-delete']
        ];
    }
}
