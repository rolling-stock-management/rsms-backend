<?php

namespace Tests\Feature\TractiveUnit;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\Status;
use App\Models\TractiveUnit;
use App\Models\User;
use Database\Seeders\Permissions\TractiveUnitPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TractiveUnitRelationshipsTest extends TestCase
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
        $this->seed(TractiveUnitPermissionsSeeder::class);
        Owner::factory()->create();
        Status::factory()->create();
        RepairWorkshop::factory()->create();
        Depot::factory()->create();
        $this->data = [
            'number' => '915200441003',
            'max_speed' => 130,
            'power_output' => 3140,
            'tractive_effort' => 294,
            'weight' => 87,
            'axle_arrangement' => 'Во′-Во′',
            'length' => 16.5,
            'brake_marking' => 'KE-GPR+E',
            'owner_id' => 1,
            'status_id' => 1,
            'repair_date' => '2021-01-01',
            'repair_valid_until' => '2022-01-01',
            'repair_workshop_id' => 1,
            'depot_id' => 1,
            'other_info' => 'R - 97t, P - 58t, G - 58t',
        ];
    }

    /**
     * Test depot can be assigned to tractive unit.
     *
     * @return void
     */
    public function testDepotCanBeAssignedToTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/tractive-units', $this->data);
        $tractiveUnit = TractiveUnit::first();

        $this->assertCount(1, TractiveUnit::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['depot']);
    }

    /**
     * Test depot can be removed from tractive unit.
     *
     * @return void
     */
    public function testDepotCanBeRemovedFromTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->patch('api/tractive-units/' . $tractiveUnit->id, array_merge($this->data, ['depot_id' => null]));
        $tractiveUnit = TractiveUnit::first();

        $this->assertEquals($this->data['number'], $tractiveUnit->number);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['depot']);
    }

    /**
     * Test owner can be assigned to tractive unit.
     *
     * @return void
     */
    public function testOwnerCanBeAssignedToTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/tractive-units', $this->data);
        $tractiveUnit = TractiveUnit::first();

        $this->assertCount(1, TractiveUnit::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['owner']);
    }

    /**
     * Test owner can be updated on tractive unit.
     *
     * @return void
     */
    public function testOwnerCanBeUpdatedOnTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $tractiveUnit = TractiveUnit::factory()->create();
        Owner::factory()->create();
        $response = $this->patch('api/tractive-units/' . $tractiveUnit->id, array_merge($this->data, ['owner_id' => 2]));
        $tractiveUnit = TractiveUnit::first();

        $this->assertEquals($this->data['number'], $tractiveUnit->number);
        $this->assertEquals(2, $tractiveUnit->owner->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test status can be assigned to tractive unit.
     *
     * @return void
     */
    public function testSatusCanBeAssignedToTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/tractive-units', $this->data);
        $tractiveUnit = TractiveUnit::first();

        $this->assertCount(1, TractiveUnit::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['status']);
    }

    /**
     * Test status can be updated on tractive unit.
     *
     * @return void
     */
    public function testStatusCanBeUpdatedOnTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $tractiveUnit = TractiveUnit::factory()->create();
        Status::factory()->create();
        $response = $this->patch('api/tractive-units/' . $tractiveUnit->id, array_merge($this->data, ['status_id' => 2]));
        $tractiveUnit = TractiveUnit::first();

        $this->assertEquals($this->data['number'], $tractiveUnit->number);
        $this->assertEquals(2, $tractiveUnit->status->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test repair workshop can be assigned to tractive unit.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeAssignedToTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/tractive-units', $this->data);
        $tractiveUnit = TractiveUnit::first();

        $this->assertCount(1, TractiveUnit::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['repair_workshop']);
    }

    /**
     * Test repair workshop can be removed from tractive unit.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeRemovedFromTractiveUnit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->patch('api/tractive-units/' . $tractiveUnit->id, array_merge($this->data, ['repair_workshop_id' => null]));
        $tractiveUnit = TractiveUnit::first();

        $this->assertEquals($this->data['number'], $tractiveUnit->number);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['repair_workshop']);
    }
}
