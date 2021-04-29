<?php

namespace Tests\Feature\Repair;

use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\Repair;
use App\Models\RepairType;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\TractiveUnit;
use App\Models\User;
use Database\Seeders\Permissions\RepairPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RepairRelationshipsTest extends TestCase
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
        RepairType::factory()->create();
        RepairWorkshop::factory()->create();
        PassengerWagon::factory()->create();
        FreightWagon::factory()->create();
        TractiveUnit::factory()->create();
        $this->data = [
            'short_description' => 'repair1',
            'type_id' => 1,
            'workshop_id' => 1,
            'repairable_type' => 1,
            'repairable_id' => 1,
            'description' => 'Some text to serve as a description of the repair...',
            'start_date' => '2021-03-31',
            'end_date' => '2021-04-21',
        ];
    }

    /**
     * Test repair type_id and workshop_id must be integers.
     *
     * @return void
     */
    public function testRepairTypeAndWorkshopIdsMustBeIntegers()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['type_id', 'workshop_id'])
            ->each(function ($field) {
                $response = $this->post('api/repairs', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/repairs', array_merge($this->data, [$field => 'aa']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test repair type_id and workshop_id must exist.
     *
     * @return void
     */
    public function testRepairTypeAndWorkshopIdsMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['type_id', 'workshop_id'])
            ->each(function ($field) {
                $response = $this->post('api/repairs', array_merge($this->data, [$field => 5]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test type can be assigned to repair.
     *
     * @return void
     */
    public function testTypeCanBeAssignedToRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', $this->data);
        Repair::first();

        $this->assertCount(1, Repair::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['type']);
    }

    /**
     * Test type can be updated on repair.
     *
     * @return void
     */
    public function testTypeCanBeUpdatedOnRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repair = Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create();
        $response = $this->patch('api/repairs/' . $repair->id, array_merge($this->data, ['type_id' => 2]));
        $repair = Repair::first();

        $this->assertEquals($this->data['short_description'], $repair->short_description);
        $this->assertEquals(2, $repair->type->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test workshop can be assigned to repair.
     *
     * @return void
     */
    public function testWorkshopCanBeAssignedToRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', $this->data);
        Repair::first();

        $this->assertCount(1, Repair::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['workshop']);
    }

    /**
     * Test workshop can be updated on repair.
     *
     * @return void
     */
    public function testWorkshopCanBeUpdatedOnRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repair = Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create();
        $response = $this->patch('api/repairs/' . $repair->id, array_merge($this->data, ['workshop_id' => 2]));
        $repair = Repair::first();

        $this->assertEquals($this->data['short_description'], $repair->short_description);
        $this->assertEquals(2, $repair->workshop->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test passenger wagon can be assigned to repair.
     *
     * @return void
     */
    public function testPassengerWagonCanBeAssignedToRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', array_merge($this->data, ['repairable_type' => 1, 'repairable_id' => 1]));
        Repair::first();

        $this->assertCount(1, Repair::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['repairable']);
        $this->assertEquals('App\Models\PassengerWagon',$response['data']['repairable_type']);
    }

    /**
     * Test passenger wagon can be updated on repair.
     *
     * @return void
     */
    public function testPassengerWagonCanBeUpdatedOnRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repair = Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create();
        $response = $this->patch('api/repairs/' . $repair->id, array_merge($this->data, ['repairable_type' => 1, 'repairable_id' => 1]));
        $repair = Repair::first();

        $this->assertEquals($this->data['short_description'], $repair->short_description);
        $this->assertEquals(1, $repair->repairable->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('App\Models\PassengerWagon',$response['data']['repairable_type']);
    }

    /**
     * Test passenger_wagon_id must exist.
     *
     * @return void
     */
    public function testPassengerWagonIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', array_merge($this->data, ['repairable_type' => 1, 'repairable_id' => 5]));
        $response->assertSessionHasErrors('repairable_id');
    }

    /**
     * Test freight wagon can be assigned to repair.
     *
     * @return void
     */
    public function testFreightWagonCanBeAssignedToRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', array_merge($this->data, ['repairable_type' => 2, 'repairable_id' => 1]));
        Repair::first();

        $this->assertCount(1, Repair::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['repairable']);
        $this->assertEquals('App\Models\FreightWagon',$response['data']['repairable_type']);
    }

    /**
     * Test freight wagon can be updated on repair.
     *
     * @return void
     */
    public function testFreightWagonCanBeUpdatedOnRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repair = Repair::factory()->for(
            FreightWagon::factory(), 'repairable'
        )->create();
        $response = $this->patch('api/repairs/' . $repair->id, array_merge($this->data, ['repairable_type' => 2, 'repairable_id' => 1]));
        $repair = Repair::first();

        $this->assertEquals($this->data['short_description'], $repair->short_description);
        $this->assertEquals(1, $repair->repairable->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('App\Models\FreightWagon',$response['data']['repairable_type']);
    }

    /**
     * Test freight_wagon_id must exist.
     *
     * @return void
     */
    public function testFreightWagonIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', array_merge($this->data, ['repairable_type' => 2, 'repairable_id' => 5]));
        $response->assertSessionHasErrors('repairable_id');
    }

    /**
     * Test tractive unit can be assigned to repair.
     *
     * @return void
     */
    public function testTractiveUnitCanBeAssignedToRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', array_merge($this->data, ['repairable_type' => 3, 'repairable_id' => 1]));
        Repair::first();

        $this->assertCount(1, Repair::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['repairable']);
        $this->assertEquals('App\Models\TractiveUnit',$response['data']['repairable_type']);
    }

    /**
     * Test tractive unit can be updated on repair.
     *
     * @return void
     */
    public function testTractiveUnitCanBeUpdatedOnRepair()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repair = Repair::factory()->for(
            TractiveUnit::factory(), 'repairable'
        )->create();
        $response = $this->patch('api/repairs/' . $repair->id, array_merge($this->data, ['repairable_type' => 3, 'repairable_id' => 1]));
        $repair = Repair::first();

        $this->assertEquals($this->data['short_description'], $repair->short_description);
        $this->assertEquals(1, $repair->repairable->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('App\Models\TractiveUnit',$response['data']['repairable_type']);
    }

    /**
     * Test tractive_unit_id must exist.
     *
     * @return void
     */
    public function testTractiveUnitIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', array_merge($this->data, ['repairable_type' => 3, 'repairable_id' => 5]));
        $response->assertSessionHasErrors('repairable_id');
    }
}
