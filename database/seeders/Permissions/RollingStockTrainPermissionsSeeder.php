<?php

namespace Database\Seeders\Permissions;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class RollingStockTrainPermissionsSeeder extends Seeder
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
            ['name' => 'rolling-stock-train-viewAny'],
            ['name' => 'rolling-stock-train-view'],
            ['name' => 'rolling-stock-train-create'],
            ['name' => 'rolling-stock-train-update'],
            ['name' => 'rolling-stock-train-delete']
        ];
    }
}
