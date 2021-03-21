<?php

namespace Tests\Feature;

use App\Models\PassengerInteriorType;
use App\Models\PassengerWagonType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\PassengerWagonTypePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PassengerWagonTypeTest extends TestCase
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
        $this->seed(PassengerWagonTypePermissionsSeeder::class);
        PassengerInteriorType::factory()->create();
        $this->data = [
            'name' => 'passengerWagonType1',
            'description' => 'Some text to serve as a description to the passenger wagon type...',
            'interior_type_id' => 1,
            'repair_valid_for' => 2
        ];
    }

    /**
     * Test user must be logged in in order to create a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/passenger-wagon-types', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, PassengerWagonType::all());
    }

    /**
     * Test user must have the 'passenger-wagon-type-create' permission in order to create a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/passenger-wagon-types', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, PassengerWagonType::all());
    }

    /**
     * Test user with 'passenger-wagon-type-create' permission can create a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagon-types', $this->data);
        $passengerWagonType = PassengerWagonType::first();

        $this->assertCount(1, PassengerWagonType::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $passengerWagonType->id,
                'name' => $passengerWagonType->name,
                'description' => $passengerWagonType->description,
                'repair_valid_for' => $passengerWagonType->repair_valid_for,
                'created_at' => $passengerWagonType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerWagonType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test interior type can be assigned to passenger wagon type when creating.
     *
     * @return void
     */
    public function testInteriorTypeCanBeAssignedToPassengerWagonTypeWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-wagon-types', $this->data);
        $passengerWagonType = PassengerWagonType::first();

        $this->assertCount(1, PassengerWagonType::all());
        $this->assertNotNull($passengerWagonType->interiorType);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertNotNull($response['data']['interior_type']);
    }

    /**
     * Test passenger wagon type name, repair_valid_for and interior_type_id are required.
     *
     * @return void
     */
    public function testPassengerWagonTypeNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'repair_valid_for', 'interior_type_id'])
            ->each(function ($field) {
                $response = $this->post('api/passenger-wagon-types', array_merge($this->data, [$field => null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test passenger wagon type name, description, repair_valid_for and interior_type_id must be of correct type.
     *
     * @return void
     */
    public function testPassengerWagonTypeFieldsMustBeOfCorrectType()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'description', 'repair_valid_for', 'interior_type_id'])
            ->each(function ($field) {
                $response = $this->post('api/passenger-wagon-types', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test passenger wagon type interior_type_id must exist in passenger_interior_types table.
     *
     * @return void
     */
    public function testPassengerWagonTypeInteriorTypeMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/passenger-wagon-types', array_merge($this->data, ['interior_type_id' => 22]));
        $response->assertSessionHasErrors('interior_type_id');
    }

    /**
     * Test user must have the 'passenger-wagon-type-viewAny' permission in order to see a list of passenger wagon types.
     *
     * @return void
     */
    public function testPassengerWagonTypesCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagon-types');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-wagon-type-viewAny' permission can see a list of passenger wagon types.
     *
     * @return void
     */
    public function testPassengerWagonTypesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerWagonType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/passenger-wagon-types');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test passenger wagon type pagination option.
     *
     * @return void
     */
    public function testPassengerWagonTypesPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerWagonType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/passenger-wagon-types?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'passenger-wagon-type-view' permission in order to view a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagonType = PassengerWagonType::factory()->create();
        $response = $this->get('api/passenger-wagon-types/' . $passengerWagonType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-wagon-type-view' permission can view a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagonType = PassengerWagonType::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/passenger-wagon-types/' . $passengerWagonType->id);

        $response->assertJson([
            'data' => [
                'id' => $passengerWagonType->id,
                'name' => $passengerWagonType->name,
                'description' => $passengerWagonType->description,
                'repair_valid_for' => $passengerWagonType->repair_valid_for,
                'created_at' => $passengerWagonType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerWagonType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-wagon-type-update' permission in order to update a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagonType = PassengerWagonType::factory()->create();
        $response = $this->patch('api/passenger-wagon-types/' . $passengerWagonType->id, $this->data);
        $passengerWagonType = PassengerWagonType::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $passengerWagonType->name);
    }

    /**
     * Test user with 'passenger-wagon-type-update' permission can update a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerWagonType = PassengerWagonType::factory()->create();
        $response = $this->patch('api/passenger-wagon-types/' . $passengerWagonType->id, $this->data);
        $passengerWagonType = PassengerWagonType::first();

        $this->assertEquals($this->data['name'], $passengerWagonType->name);
        $this->assertEquals($this->data['description'], $passengerWagonType->description);
        $this->assertEquals($this->data['repair_valid_for'], $passengerWagonType->repair_valid_for);
        $this->assertEquals($this->data['interior_type_id'], $passengerWagonType->interiorType->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $passengerWagonType->id,
                'name' => $passengerWagonType->name,
                'description' => $passengerWagonType->description,
                'repair_valid_for' => $passengerWagonType->repair_valid_for,
                'created_at' => $passengerWagonType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerWagonType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-wagon-type-delete' permission in order to delete a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerWagonType = PassengerWagonType::factory()->create();
        $response = $this->delete('api/passenger-wagon-types/' . $passengerWagonType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, PassengerWagonType::all());
    }

    /**
     * Test user with 'passenger-wagon-type-delete' permission can delete a passenger wagon type.
     *
     * @return void
     */
    public function testPassengerWagonTypeCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $passengerWagonType = PassengerWagonType::factory()->create();
        $response = $this->delete('api/passenger-wagon-types/' . $passengerWagonType->id);

        $this->assertCount(0, PassengerWagonType::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
