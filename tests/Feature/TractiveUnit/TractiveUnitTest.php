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
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TractiveUnitTest extends TestCase
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
     * Test user must be logged in in order to create a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/tractive-units', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, TractiveUnit::all());
    }

    /**
     * Test user must have the 'tractive-unit-create' permission in order to create a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/tractive-units', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, TractiveUnit::all());
    }

    /**
     * Test user with 'tractive-unit-create' permission can create a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCanBeCreatedWithTheRightPermission()
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
        $response->assertJson([
            'data' => [
                'id' => $tractiveUnit->id,
                'number' => $tractiveUnit->number,
                'stylized_number' => $tractiveUnit->getStylizedNumber(),
                'max_speed' => $tractiveUnit->max_speed,
                'power_output' => $tractiveUnit->power_output,
                'tractive_effort' => $tractiveUnit->tractive_effort,
                'weight' => $tractiveUnit->weight,
                'axle_arrangement' => $tractiveUnit->axle_arrangement,
                'length' => $tractiveUnit->length,
                'brake_marking' => $tractiveUnit->brake_marking,
                'repair_date' => $tractiveUnit->repair_date->format('d.m.Y h:i:s'),
                'repair_valid_until' => $tractiveUnit->repair_valid_until->format('d.m.Y h:i:s'),
                'other_info' => $tractiveUnit->other_info,
                'created_at' => $tractiveUnit->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $tractiveUnit->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'tractive-unit-viewAny' permission in order to see a list of tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/tractive-units');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'tractive-unit-viewAny' permission can see a list of tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        TractiveUnit::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/tractive-units');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'tractive-unit-view' permission in order to view a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->get('api/tractive-units/' . $tractiveUnit->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'tractive-unit-view' permission can view a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $tractiveUnit = TractiveUnit::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/tractive-units/' . $tractiveUnit->id);

        $response->assertJson([
            'data' => [
                'id' => $tractiveUnit->id,
                'number' => $tractiveUnit->number,
                'stylized_number' => $tractiveUnit->getStylizedNumber(),
                'max_speed' => $tractiveUnit->max_speed,
                'power_output' => $tractiveUnit->power_output,
                'tractive_effort' => $tractiveUnit->tractive_effort,
                'weight' => $tractiveUnit->weight,
                'axle_arrangement' => $tractiveUnit->axle_arrangement,
                'length' => $tractiveUnit->length,
                'brake_marking' => $tractiveUnit->brake_marking,
                'repair_date' => $tractiveUnit->repair_date->format('d.m.Y h:i:s'),
                'repair_valid_until' => $tractiveUnit->repair_valid_until->format('d.m.Y h:i:s'),
                'other_info' => $tractiveUnit->other_info,
                'created_at' => $tractiveUnit->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $tractiveUnit->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'tractive-unit-update' permission in order to update a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->patch('api/tractive-units/' . $tractiveUnit->id, $this->data);
        $tractiveUnit = TractiveUnit::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['number'], $tractiveUnit->number);
    }

    /**
     * Test user with 'tractive-unit-update' permission can update a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->patch('api/tractive-units/' . $tractiveUnit->id, $this->data);
        $tractiveUnit = TractiveUnit::first();

        $this->assertEquals($this->data['number'], $tractiveUnit->number);
        $this->assertEquals($this->data['max_speed'], $tractiveUnit->max_speed);
        $this->assertEquals($this->data['power_output'], $tractiveUnit->power_output);
        $this->assertEquals($this->data['tractive_effort'], $tractiveUnit->tractive_effort);
        $this->assertEquals($this->data['weight'], $tractiveUnit->weight);
        $this->assertEquals($this->data['axle_arrangement'], $tractiveUnit->axle_arrangement);
        $this->assertEquals($this->data['length'], $tractiveUnit->length);
        $this->assertEquals($this->data['brake_marking'], $tractiveUnit->brake_marking);
        $this->assertEquals($this->data['repair_date'], $tractiveUnit->repair_date->format('Y-m-d'));
        $this->assertEquals($this->data['repair_valid_until'], $tractiveUnit->repair_valid_until->format('Y-m-d'));
        $this->assertEquals($this->data['other_info'], $tractiveUnit->other_info);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $tractiveUnit->id,
                'number' => $tractiveUnit->number,
                'stylized_number' => $tractiveUnit->getStylizedNumber(),
                'max_speed' => $tractiveUnit->max_speed,
                'power_output' => $tractiveUnit->power_output,
                'tractive_effort' => $tractiveUnit->tractive_effort,
                'weight' => $tractiveUnit->weight,
                'axle_arrangement' => $tractiveUnit->axle_arrangement,
                'length' => $tractiveUnit->length,
                'brake_marking' => $tractiveUnit->brake_marking,
                'repair_date' => $tractiveUnit->repair_date->format('d.m.Y h:i:s'),
                'repair_valid_until' => $tractiveUnit->repair_valid_until->format('d.m.Y h:i:s'),
                'other_info' => $tractiveUnit->other_info,
                'created_at' => $tractiveUnit->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $tractiveUnit->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'tractive-unit-delete' permission in order to delete a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->delete('api/tractive-units/' . $tractiveUnit->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, TractiveUnit::all());
    }

    /**
     * Test user with 'tractive-unit-delete' permission can delete a tractive unit.
     *
     * @return void
     */
    public function testTractiveUnitCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $tractiveUnit = TractiveUnit::factory()->create();
        $response = $this->delete('api/tractive-units/' . $tractiveUnit->id);

        $this->assertCount(0, TractiveUnit::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
