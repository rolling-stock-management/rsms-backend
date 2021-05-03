<?php

namespace Tests\Feature\FreightWagon;

use App\Models\Depot;
use App\Models\FreightWagon;
use App\Models\FreightWagonType;
use App\Models\Owner;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\Permissions\FreightWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FreightWagonRelationshipsTest extends TestCase
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
        $this->seed(FreightWagonPermissionsSeeder::class);
        FreightWagonType::factory()->create(['name'=>'F']);
        Owner::factory()->create();
        Status::factory()->create();
        RepairWorkshop::factory()->create();
        Depot::factory()->create();
        $this->data = [
            'number' => '845266510708',
            'type_id' => 1,
            'letter_marking' => 'Fals',
            'tare' => 24.950,
            'weight_capacity' => 50,
            'length_capacity' => 10,
            'volume_capacity' => 75,
            'area_capacity' => 30,
            'max_speed' => 100,
            'length' => 12.79,
            'brake_marking' => 'KE-GP',
            'owner_id' => 1,
            'status_id' => 1,
            'repair_date' => '2021-01-01',
            'repair_valid_until' => '2022-01-01',
            'repair_workshop_id' => 1,
            'depot_id' => 1,
            'other_info' => 'S:A - 39t, B1 - 39t, B2 - 47t, C - 55t',
        ];
    }

    /**
     * Test freight wagon type_id, owner_id, status_id, repair_workshop_id, depot_id must be integers.
     *
     * @return void
     */
    public function testFreightWagonForeignKeyIdsMustBeIntegers()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['type_id', 'owner_id', 'status_id', 'repair_workshop_id', 'depot_id'])
            ->each(function ($field) {
                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test freight wagon type_id, owner_id, status_id, repair_workshop_id, depot_id must exist.
     *
     * @return void
     */
    public function testFreightWagonForeignKeyIdsMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['type_id', 'owner_id', 'status_id', 'repair_workshop_id', 'depot_id'])
            ->each(function ($field) {
                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => 5]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test freight wagon type can be assigned to freight wagon.
     *
     * @return void
     */
    public function testFreightWagonTypeCanBeAssignedToFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagons', $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertCount(1, FreightWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['type']);
    }

    /**
     * Test freight wagon type can be updated on freight wagon.
     *
     * @return void
     */
    public function testFreightWagonTypeCanBeUpdatedOnFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagon = FreightWagon::factory()->create();
        Owner::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, array_merge($this->data, ['type_id' => 2]));
        $freightWagon = FreightWagon::first();

        $this->assertEquals($this->data['number'], $freightWagon->number);
        $this->assertEquals(2, $freightWagon->type->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test depot can be assigned to freight wagon.
     *
     * @return void
     */
    public function testDepotCanBeAssignedToFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagons', $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertCount(1, FreightWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['depot']);
    }

    /**
     * Test depot can be removed from freight wagon.
     *
     * @return void
     */
    public function testDepotCanBeRemovedFromFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, array_merge($this->data, ['depot_id' => null]));
        $freightWagon = FreightWagon::first();

        $this->assertEquals($this->data['number'], $freightWagon->number);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['depot']);
    }

    /**
     * Test owner can be assigned to freight wagon.
     *
     * @return void
     */
    public function testOwnerCanBeAssignedToFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagons', $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertCount(1, FreightWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['owner']);
    }

    /**
     * Test owner can be updated on freight wagon.
     *
     * @return void
     */
    public function testOwnerCanBeUpdatedOnFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagon = FreightWagon::factory()->create();
        Owner::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, array_merge($this->data, ['owner_id' => 2]));
        $freightWagon = FreightWagon::first();

        $this->assertEquals($this->data['number'], $freightWagon->number);
        $this->assertEquals(2, $freightWagon->owner->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test status can be assigned to freight wagon.
     *
     * @return void
     */
    public function testSatusCanBeAssignedToFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagons', $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertCount(1, FreightWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['status']);
    }

    /**
     * Test status can be updated on freight wagon.
     *
     * @return void
     */
    public function testStatusCanBeUpdatedOnFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagon = FreightWagon::factory()->create();
        Status::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, array_merge($this->data, ['status_id' => 2]));
        $freightWagon = FreightWagon::first();

        $this->assertEquals($this->data['number'], $freightWagon->number);
        $this->assertEquals(2, $freightWagon->status->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test repair workshop can be assigned to freight wagon.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeAssignedToFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagons', $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertCount(1, FreightWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['repair_workshop']);
    }

    /**
     * Test repair workshop can be removed from freight wagon.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeRemovedFromFreightWagon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, array_merge($this->data, ['repair_workshop_id' => null]));
        $freightWagon = FreightWagon::first();

        $this->assertEquals($this->data['number'], $freightWagon->number);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['repair_workshop']);
    }
}
