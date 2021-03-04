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

class PassengerWagonTest extends TestCase
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
        PassengerWagonType::factory()->create(['name'=>'19-40']);
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
     * Test user must be logged in in order to create a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/passenger-wagons', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, PassengerWagon::all());
    }

    /**
     * Test user must have the 'passenger-wagon-create' permission in order to create a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/passenger-wagons', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, PassengerWagon::all());
    }

    /**
     * Test user with 'passenger-wagon-create' permission can create a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCanBeCreatedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagons', $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertCount(1, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $passengerWagon->id,
                'number' => $passengerWagon->number,
                'stylized_number' => $passengerWagon->getStylizedNumber(),
                'letter_marking' => $passengerWagon->letter_marking,
                'tare' => $passengerWagon->tare,
                'total_weight' => $passengerWagon->total_weight,
                'seats_count' => $passengerWagon->seats_count,
                'max_speed' => $passengerWagon->max_speed,
                'length' => $passengerWagon->length,
                'brake_marking' => $passengerWagon->brake_marking,
                'repair_date' => $passengerWagon->repair_date->format('Y-m-d'),
                'repair_valid_until' => $passengerWagon->repair_valid_until->format('Y-m-d'),
                'other_info' => $passengerWagon->other_info,
                'created_at' => $passengerWagon->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerWagon->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-wagon-viewAny' permission in order to see a list of passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagons');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-wagon-viewAny' permission can see a list of passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerWagon::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/passenger-wagons');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'passenger-wagon-view' permission in order to view a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->get('api/passenger-wagons/' . $passengerWagon->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-wagon-view' permission can view a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagon = PassengerWagon::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/passenger-wagons/' . $passengerWagon->id);

        $response->assertJson([
            'data' => [
                'id' => $passengerWagon->id,
                'number' => $passengerWagon->number,
                'stylized_number' => $passengerWagon->getStylizedNumber(),
                'letter_marking' => $passengerWagon->letter_marking,
                'tare' => $passengerWagon->tare,
                'total_weight' => $passengerWagon->total_weight,
                'seats_count' => $passengerWagon->seats_count,
                'max_speed' => $passengerWagon->max_speed,
                'length' => $passengerWagon->length,
                'brake_marking' => $passengerWagon->brake_marking,
                'repair_date' => $passengerWagon->repair_date->format('Y-m-d'),
                'repair_valid_until' => $passengerWagon->repair_valid_until->format('Y-m-d'),
                'other_info' => $passengerWagon->other_info,
                'created_at' => $passengerWagon->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerWagon->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-wagon-update' permission in order to update a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->patch('api/passenger-wagons/' . $passengerWagon->id, $this->data);
        $passengerWagon = PassengerWagon::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['number'], $passengerWagon->number);
    }

    /**
     * Test user with 'passenger-wagon-update' permission can update a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->patch('api/passenger-wagons/' . $passengerWagon->id, $this->data);
        $passengerWagon = PassengerWagon::first();

        $this->assertEquals($this->data['number'], $passengerWagon->number);
        $this->assertEquals($this->data['letter_marking'], $passengerWagon->letter_marking);
        $this->assertEquals($this->data['tare'], $passengerWagon->tare);
        $this->assertEquals($this->data['total_weight'], $passengerWagon->total_weight);
        $this->assertEquals($this->data['seats_count'], $passengerWagon->seats_count);
        $this->assertEquals($this->data['max_speed'], $passengerWagon->max_speed);
        $this->assertEquals($this->data['length'], $passengerWagon->length);
        $this->assertEquals($this->data['brake_marking'], $passengerWagon->brake_marking);
        $this->assertEquals($this->data['repair_date'], $passengerWagon->repair_date->format('Y-m-d'));
        $this->assertEquals($this->data['other_info'], $passengerWagon->other_info);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $passengerWagon->id,
                'number' => $passengerWagon->number,
                'stylized_number' => $passengerWagon->getStylizedNumber(),
                'letter_marking' => $passengerWagon->letter_marking,
                'tare' => $passengerWagon->tare,
                'total_weight' => $passengerWagon->total_weight,
                'seats_count' => $passengerWagon->seats_count,
                'max_speed' => $passengerWagon->max_speed,
                'length' => $passengerWagon->length,
                'brake_marking' => $passengerWagon->brake_marking,
                'repair_date' => $passengerWagon->repair_date->format('Y-m-d'),
                'repair_valid_until' => $passengerWagon->repair_valid_until->format('Y-m-d'),
                'other_info' => $passengerWagon->other_info,
                'created_at' => $passengerWagon->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerWagon->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-wagon-delete' permission in order to delete a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->delete('api/passenger-wagons/' . $passengerWagon->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, PassengerWagon::all());
    }

    /**
     * Test user with 'passenger-wagon-delete' permission can delete a passenger wagon.
     *
     * @return void
     */
    public function testPassengerWagonCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $passengerWagon = PassengerWagon::factory()->create();
        $response = $this->delete('api/passenger-wagons/' . $passengerWagon->id);

        $this->assertCount(0, PassengerWagon::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
