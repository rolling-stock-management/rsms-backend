<?php

namespace Tests\Feature\PassengerWagon;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\PassengerWagon;
use App\Models\PassengerWagonType;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\Permissions\PassengerWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PassengerWagonRelationshipsTest extends TestCase
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
        $this->seed(PassengerWagonPermissionsSeeder::class);
        PassengerWagonType::factory()->create(['name' => '19-40']);
        Owner::factory()->create();
        Status::factory()->create();
        RepairWorkshop::factory()->create();
        Depot::factory()->create();
        $this->data = [
            'number' => '515219401400',
            'letter_marking' => 'Ame',
            'tare' => 38,
            'total_weight' => 42,
            'seats_count' => 54,
            'max_speed' => 140,
            'length' => 24.5,
            'brake_marking' => 'KE-GPR',
            'owner_id' => 1,
            'status_id' => 1,
            'repair_date' => '2021-01-01',
            'repair_workshop_id' => 1,
            'depot_id' => 1,
            'other_info' => 'R - 60t, P - 42t, G - 40t',
        ];
    }


    /**
     * Test passenger wagon type is properly assigned to passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonTypeIsPropperlyAssignedToPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagons', $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertCount(1, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['depot']);
        $this->assertEquals('19-40', $passengerWagon->type->name);
    }

    /**
     * Test depot can be assigned to passenger wagon.
     *
     * @return void
     */
    public function testDepotCanBeAssignedToPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagons', $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertCount(1, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['depot']);
    }

    /**
     * Test depot can be removed from passenger wagon.
     *
     * @return void
     */
    public function testDepotCanBeRemovedFromPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->patch('api/passenger-wagons/' . $passengerWagon->id, array_merge($this->data, ['depot_id' => null]));
        $passengerWagon = PassengerWagon::first();

        $this->assertEquals($this->data['number'], $passengerWagon->number);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['depot']);
    }

    /**
     * Test owner can be assigned to passenger wagon.
     *
     * @return void
     */
    public function testOwnerCanBeAssignedToPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagons', $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertCount(1, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['owner']);
    }

    /**
     * Test owner can be updated on passenger wagon.
     *
     * @return void
     */
    public function testOwnerCanBeUpdatedOnPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerWagon = PassengerWagon::factory()->create();
        Owner::factory()->create();
        $response = $this->patch('api/passenger-wagons/' . $passengerWagon->id, array_merge($this->data, ['owner_id' => 2]));
        $passengerWagon = PassengerWagon::first();

        $this->assertEquals($this->data['number'], $passengerWagon->number);
        $this->assertEquals(2, $passengerWagon->owner->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test status can be assigned to passenger wagon.
     *
     * @return void
     */
    public function testSatusCanBeAssignedToPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagons', $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertCount(1, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['status']);
    }

    /**
     * Test status can be updated on passenger wagon.
     *
     * @return void
     */
    public function testStatusCanBeUpdatedOnPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerWagon = PassengerWagon::factory()->create();
        Status::factory()->create();
        $response = $this->patch('api/passenger-wagons/' . $passengerWagon->id, array_merge($this->data, ['status_id' => 2]));
        $passengerWagon = PassengerWagon::first();

        $this->assertEquals($this->data['number'], $passengerWagon->number);
        $this->assertEquals(2, $passengerWagon->status->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test repair workshop can be assigned to passenger wagon.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeAssignedToPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagons', $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertCount(1, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['repair_workshop']);
    }

    /**
     * Test repair workshop can be removed from passenger wagon.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeRemovedFromPassengerWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->patch('api/passenger-wagons/' . $passengerWagon->id, array_merge($this->data, ['repair_workshop_id' => null]));
        $passengerWagon = PassengerWagon::first();

        $this->assertEquals($this->data['number'], $passengerWagon->number);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['repair_workshop']);
    }
}
