<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class RepairWorkshopPermissionsSeeder extends Seeder
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
            ['name' => 'repair-workshop-viewAny'],
            ['name' => 'repair-workshop-view'],
            ['name' => 'repair-workshop-create'],
            ['name' => 'repair-workshop-update'],
            ['name' => 'repair-workshop-delete']
        ];
    }
}
