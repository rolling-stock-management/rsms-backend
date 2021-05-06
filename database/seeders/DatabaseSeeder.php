<?php

namespace Database\Seeders;

use Database\Seeders\Permissions\DepotPermissionSeeder;
use Database\Seeders\Permissions\FreightWagonPermissionsSeeder;
use Database\Seeders\Permissions\FreightWagonTypePermissionsSeeder;
use Database\Seeders\Permissions\OwnerPermissionsSeeder;
use Database\Seeders\Permissions\PassengerInteriorTypePermissionsSeeder;
use Database\Seeders\Permissions\PassengerReportPermissionsSeeder;
use Database\Seeders\Permissions\PassengerWagonPermissionsSeeder;
use Database\Seeders\Permissions\PassengerWagonTypePermissionsSeeder;
use Database\Seeders\Permissions\PermissionPermissionsSeeder;
use Database\Seeders\Permissions\RepairPermissionsSeeder;
use Database\Seeders\Permissions\RepairTypePermissionsSeeder;
use Database\Seeders\Permissions\RepairWorkshopPermissionsSeeder;
use Database\Seeders\Permissions\RolePermissionsSeeder;
use Database\Seeders\Permissions\RollingStockTrainPermissionsSeeder;
use Database\Seeders\Permissions\StatusPermissionsSeeder;
use Database\Seeders\Permissions\TimetablePermissionsSeeder;
use Database\Seeders\Permissions\TractiveUnitPermissionsSeeder;
use Database\Seeders\Permissions\TrainPermissionsSeeder;
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
        //Permissions
        $this->call(PermissionPermissionsSeeder::class);
        $this->call(RolePermissionsSeeder::class);
        $this->call(UserPermissionsSeeder::class);
        $this->call(DepotPermissionSeeder::class);
        $this->call(PassengerInteriorTypePermissionsSeeder::class);
        $this->call(PassengerWagonTypePermissionsSeeder::class);
        $this->call(FreightWagonTypePermissionsSeeder::class);
        $this->call(StatusPermissionsSeeder::class);
        $this->call(RepairTypePermissionsSeeder::class);
        $this->call(OwnerPermissionsSeeder::class);
        $this->call(RepairWorkshopPermissionsSeeder::class);
        $this->call(TractiveUnitPermissionsSeeder::class);
        $this->call(PassengerWagonPermissionsSeeder::class);
        $this->call(FreightWagonPermissionsSeeder::class);
        $this->call(RepairPermissionsSeeder::class);
        $this->call(TimetablePermissionsSeeder::class);
        $this->call(TrainPermissionsSeeder::class);
        $this->call(RollingStockTrainPermissionsSeeder::class);
        $this->call(PassengerReportPermissionsSeeder::class);
        //Models
        $this->call(RoleSeeder::class);
        $this->call(DepotSeeder::class);
        $this->call(PassengerInteriorTypeSeeder::class);
        $this->call(PassengerWagonTypeSeeder::class);
        $this->call(FreightWagonTypeSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(RepairTypeSeeder::class);
        $this->call(OwnerSeeder::class);
        $this->call(RepairWorkshopSeeder::class);
        $this->call(TimetableSeeder::class);
        $this->call(TrainSeeder::class);
    }
}
