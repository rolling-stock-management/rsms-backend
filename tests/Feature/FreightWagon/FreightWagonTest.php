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
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FreightWagonTest extends TestCase
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
     * Test user must be logged in in order to create a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/freight-wagons', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, FreightWagon::all());
    }

    /**
     * Test user must have the 'freight-wagon-create' permission in order to create a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/freight-wagons', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, FreightWagon::all());
    }

    /**
     * Test user with 'freight-wagon-create' permission can create a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCanBeCreatedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagons', $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertCount(1, FreightWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $freightWagon->id,
                'number' => $freightWagon->number,
                'stylized_number' => $freightWagon->getStylizedNumber(),
                'letter_marking' => $freightWagon->letter_marking,
                'tare' => $freightWagon->tare,
                'weight_capacity' => $freightWagon->weight_capacity,
                'length_capacity' => $freightWagon->length_capacity,
                'volume_capacity' => $freightWagon->volume_capacity,
                'area_capacity' => $freightWagon->area_capacity,
                'max_speed' => $freightWagon->max_speed,
                'length' => $freightWagon->length,
                'brake_marking' => $freightWagon->brake_marking,
                'repair_date' => $freightWagon->repair_date->format('Y-m-d'),
                'repair_valid_until' => $freightWagon->repair_valid_until ? $freightWagon->repair_valid_until->format('Y-m-d') : null,
                'other_info' => $freightWagon->other_info,
                'created_at' => $freightWagon->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $freightWagon->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'freight-wagon-viewAny' permission in order to see a list of freight wagon.
     *
     * @return void
     */
    public function testFreightWagonsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagons');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'freight-wagon-viewAny' permission can see a list of freight wagon.
     *
     * @return void
     */
    public function testFreightWagonesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        FreightWagon::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/freight-wagons');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'freight-wagon-view' permission in order to view a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->get('api/freight-wagons/' . $freightWagon->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'freight-wagon-view' permission can view a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagon = FreightWagon::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/freight-wagons/' . $freightWagon->id);

        $response->assertJson([
            'data' => [
                'id' => $freightWagon->id,
                'number' => $freightWagon->number,
                'stylized_number' => $freightWagon->getStylizedNumber(),
                'letter_marking' => $freightWagon->letter_marking,
                'tare' => $freightWagon->tare,
                'weight_capacity' => $freightWagon->weight_capacity,
                'length_capacity' => $freightWagon->length_capacity,
                'volume_capacity' => $freightWagon->volume_capacity,
                'area_capacity' => $freightWagon->area_capacity,
                'max_speed' => $freightWagon->max_speed,
                'length' => $freightWagon->length,
                'brake_marking' => $freightWagon->brake_marking,
                'repair_date' => $freightWagon->repair_date->format('Y-m-d'),
                'repair_valid_until' => $freightWagon->repair_valid_until ? $freightWagon->repair_valid_until->format('Y-m-d') : null,
                'other_info' => $freightWagon->other_info,
                'created_at' => $freightWagon->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $freightWagon->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'freight-wagon-update' permission in order to update a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, $this->data);
        $freightWagon = FreightWagon::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['number'], $freightWagon->number);
    }

    /**
     * Test user with 'freight-wagon-update' permission can update a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCanBeUpdatedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->patch('api/freight-wagons/' . $freightWagon->id, $this->data);
        $freightWagon = FreightWagon::first();

        $this->assertEquals($this->data['number'], $freightWagon->number);
        $this->assertEquals($this->data['letter_marking'], $freightWagon->letter_marking);
        $this->assertEquals($this->data['tare'], $freightWagon->tare);
        $this->assertEquals($this->data['weight_capacity'], $freightWagon->weight_capacity);
        $this->assertEquals($this->data['length_capacity'], $freightWagon->length_capacity);
        $this->assertEquals($this->data['volume_capacity'], $freightWagon->volume_capacity);
        $this->assertEquals($this->data['area_capacity'], $freightWagon->area_capacity);
        $this->assertEquals($this->data['max_speed'], $freightWagon->max_speed);
        $this->assertEquals($this->data['length'], $freightWagon->length);
        $this->assertEquals($this->data['brake_marking'], $freightWagon->brake_marking);
        $this->assertEquals($this->data['repair_date'], $freightWagon->repair_date->format('Y-m-d'));
        $this->assertEquals($this->data['other_info'], $freightWagon->other_info);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $freightWagon->id,
                'number' => $freightWagon->number,
                'stylized_number' => $freightWagon->getStylizedNumber(),
                'letter_marking' => $freightWagon->letter_marking,
                'tare' => $freightWagon->tare,
                'weight_capacity' => $freightWagon->weight_capacity,
                'length_capacity' => $freightWagon->length_capacity,
                'volume_capacity' => $freightWagon->volume_capacity,
                'area_capacity' => $freightWagon->area_capacity,
                'max_speed' => $freightWagon->max_speed,
                'length' => $freightWagon->length,
                'brake_marking' => $freightWagon->brake_marking,
                'repair_date' => $freightWagon->repair_date->format('Y-m-d'),
                'repair_valid_until' => $freightWagon->repair_valid_until->format('Y-m-d'),
                'other_info' => $freightWagon->other_info,
                'created_at' => $freightWagon->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $freightWagon->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'freight-wagon-delete' permission in order to delete a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->delete('api/freight-wagons/' . $freightWagon->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, FreightWagon::all());
    }

    /**
     * Test user with 'freight-wagon-delete' permission can delete a freight wagon.
     *
     * @return void
     */
    public function testFreightWagonCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $freightWagon = FreightWagon::factory()->create();
        $response = $this->delete('api/freight-wagons/' . $freightWagon->id);

        $this->assertCount(0, FreightWagon::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
