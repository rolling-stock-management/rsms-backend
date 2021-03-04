<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Passenger wagons manager role
        $rolePassenger = Role::factory()->create(['name' => 'passenger-manager']);
        $rolePassenger->permissions()->sync([12, 17, 22, 32, 37, 42, 47, 57, 58, 59, 60, 61]);
        $rolePassenger->save();

        //Freight wagons manager role
        $roleFreight = Role::factory()->create(['name' => 'freight-manager']);
        $roleFreight->permissions()->sync([12, 27, 32, 37, 42, 47, 62, 63, 64, 65, 66]);
        $roleFreight->save();

        //Tractive units manager role
        $roleLocomotive = Role::factory()->create(['name' => 'locmotive-manager']);
        $roleLocomotive->permissions()->sync([12, 32, 37, 42, 47, 52, 53, 54, 55, 56]);
        $roleLocomotive->save();

        //Passenger wagons reporter role
        $roleReporter = Role::factory()->create(['name' => 'passenger-reporter']);
        $roleReporter->permissions()->sync([12, 22, 42, 47, 57]);
        $roleReporter->save();

        //Administrator role
        $roleAdministrator = Role::factory()->create(['name' => 'administrator']);
        $roleAdministrator->permissions()->sync(range(1, 51));
        $roleAdministrator->save();
    }
}
