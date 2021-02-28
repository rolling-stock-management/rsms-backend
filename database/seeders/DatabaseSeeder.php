<?php

namespace Database\Seeders;

use Database\Seeders\Permissions\DepotPermissionSeeder;
use Database\Seeders\Permissions\PermissionPermissionsSeeder;
use Database\Seeders\Permissions\RolePermissionsSeeder;
use Database\Seeders\Permissions\UserPermissionsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionPermissionsSeeder::class);
        $this->call(RolePermissionsSeeder::class);
        $this->call(UserPermissionsSeeder::class);
        $this->call(DepotPermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(DepotSeeder::class);
    }
}
