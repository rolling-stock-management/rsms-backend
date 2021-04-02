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

    /**
     * Test repairs can be filtered by type id.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByTypeId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create();

        $response = $this->get('api/repairs' . '?type_id=1');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test repairs can be filtered by workshop id.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByWorkshopId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create();

        $response = $this->get('api/repairs' . '?workshop_id=1');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test repairs can be filtered by starting date before a given date.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByStartDateBefore()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['start_date' => '2021-03-02']);
        Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['start_date' => '2021-05-02']);

        $response = $this->get('api/repairs' . '?start_date_before=2021-04-01');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test repairs can be filtered by starting date after a given date.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByStartDateAfter()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['start_date' => '2021-04-02']);
        Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create();

        $response = $this->get('api/repairs' . '?start_date_after=2021-03-01');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test repairs can be filtered by ending date before a given date.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByEndDateBefore()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['end_date' => '2021-03-02']);
        Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['end_date' => '2021-05-02']);

        $response = $this->get('api/repairs' . '?end_date_before=2021-04-01');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test repairs can be filtered by ending date after a given date.
     *
     * @return void
     */
    public function testRepairsCanBeFilteredByEndDateAfter()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        Repair::factory()->count(2)->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['end_date' => '2021-04-02']);
        Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create(['end_date' => '2021-02-02']);

        $response = $this->get('api/repairs' . '?end_date_after=2021-03-01');

        $response->assertJsonCount(2, 'data');
    }

}
