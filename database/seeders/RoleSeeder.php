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
        $rolePassenger->permissions()->sync(array_merge([12, 17, 22, 32, 37, 42, 47, 57, 58, 59, 60, 61, 67, 68, 69, 70, 71, 77, 78], range(82, 86)));
        $rolePassenger->save();

        //Freight wagons manager role
        $roleFreight = Role::factory()->create(['name' => 'freight-manager']);
        $roleFreight->permissions()->sync(array_merge([12, 27, 32, 37, 42, 47, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 77, 78], range(82, 86)));
        $roleFreight->save();

        //Tractive units manager role
        $roleLocomotive = Role::factory()->create(['name' => 'locomotive-manager']);
        $roleLocomotive->permissions()->sync(array_merge([12, 32, 37, 42, 47, 52, 53, 54, 55, 56, 67, 68, 69, 70, 71, 77, 78], range(82, 86)));
        $roleLocomotive->save();

        //Passenger wagons reporter role
        $roleReporter = Role::factory()->create(['name' => 'passenger-reporter']);
        $roleReporter->permissions()->sync(array_merge([12, 22, 42, 47, 57, 77, 78],range(82, 90)));
        $roleReporter->save();

        //Administrator role
        $roleAdministrator = Role::factory()->create(['name' => 'administrator']);
        $roleAdministrator->permissions()->sync(array_merge(range(1, 51), range(72, 81)));
        $roleAdministrator->save();
    }
}
