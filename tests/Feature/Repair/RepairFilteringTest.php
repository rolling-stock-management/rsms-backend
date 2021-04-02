<?php

namespace Tests\Feature\Repair;

use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\Repair;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RepairPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RepairFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(RepairPermissionsSeeder::class);
    }

    /**
     * Test repairs can be filtered by repairable type.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByRepairableType()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->for(
            FreightWagon::factory(), 'repairable'
        )->create();
        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create();


        $response = $this->get('api/repairs' . '?repairable_type=1');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test repairs cannot be filtered by repairable type with invalid id.
     *
     * @return void
     */
    public function testRepairsCannotBeFilteredByRepairableTypeWithInvalidId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->for(
            FreightWagon::factory(), 'repairable'
        )->create();
        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create();


        $response = $this->get('api/repairs' . '?repairable_type=10');

        $response->assertJsonCount(3, 'data');
    }
}
