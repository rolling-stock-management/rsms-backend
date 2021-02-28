<?php

namespace Tests\Feature;

use App\Models\PassengerInteriorType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\PassengerInteriorTypePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PassengerInteriorTypeTest extends TestCase
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
        $this->seed(PassengerInteriorTypePermissionsSeeder::class);
        $this->data = [
            'name' => 'passengerInteriorType1',
            'description' => 'Some text to serve as a description to the passenger interior type...'
        ];
    }

    /**
     * Test user must be logged in in order to create a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/passenger-interior-types', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, PassengerInteriorType::all());
    }

    /**
     * Test user must have the 'passenger-interior-type-create' permission in order to create a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/passenger-interior-types', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, PassengerInteriorType::all());
    }

    /**
     * Test user with 'passenger-interior-type-create' permission can create a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/passenger-interior-types', $this->data);
        $passengerInteriorType = PassengerInteriorType::first();

        $this->assertCount(1, PassengerInteriorType::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $passengerInteriorType->id,
                'name' => $passengerInteriorType->name,
                'description' => $passengerInteriorType->description,
                'created_at' => $passengerInteriorType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerInteriorType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test passenger interior type name is required.
     *
     * @return void
     */
    public function testPassengerInteriorTypeNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/passenger-interior-types', array_merge($this->data, ['name' => null]));

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test passenger interior type name and description must be strings.
     *
     * @return void
     */
    public function testPassengerInteriorTypeNameAndDescriptionMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'description'])
            ->each(function ($field) {
                $response = $this->post('api/passenger-interior-types', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'passenger-interior-type-viewAny' permission in order to see a list of passenger interior types.
     *
     * @return void
     */
    public function testPassengerInteriorTypesCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-interior-types');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-interior-type-viewAny' permission can see a list of passenger interior types.
     *
     * @return void
     */
    public function testPassengerInteriorTypesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerInteriorType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/passenger-interior-types');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test passenger interior type pagination option.
     *
     * @return void
     */
    public function testPassengerInteriorTypesPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerInteriorType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/passenger-interior-types?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'passenger-interior-type-view' permission in order to view a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerInteriorType = PassengerInteriorType::factory()->create();
        $response = $this->get('api/passenger-interior-types/' . $passengerInteriorType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-interior-type-view' permission can view a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerInteriorType = PassengerInteriorType::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/passenger-interior-types/' . $passengerInteriorType->id);

        $response->assertJson([
            'data' => [
                'id' => $passengerInteriorType->id,
                'name' => $passengerInteriorType->name,
                'description' => $passengerInteriorType->description,
                'created_at' => $passengerInteriorType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerInteriorType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-interior-type-update' permission in order to update a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerInteriorType = PassengerInteriorType::factory()->create();
        $response = $this->patch('api/passenger-interior-types/' . $passengerInteriorType->id, $this->data);
        $passengerInteriorType = PassengerInteriorType::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $passengerInteriorType->name);
    }

    /**
     * Test user with 'passenger-interior-type-update' permission can update a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerInteriorType = PassengerInteriorType::factory()->create();
        $response = $this->patch('api/passenger-interior-types/' . $passengerInteriorType->id, $this->data);
        $passengerInteriorType = PassengerInteriorType::first();

        $this->assertEquals($this->data['name'], $passengerInteriorType->name);
        $this->assertEquals($this->data['description'], $passengerInteriorType->description);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $passengerInteriorType->id,
                'name' => $passengerInteriorType->name,
                'description' => $passengerInteriorType->description,
                'created_at' => $passengerInteriorType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerInteriorType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-interior-type-delete' permission in order to delete a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerInteriorType = PassengerInteriorType::factory()->create();
        $response = $this->delete('api/passenger-interior-types/' . $passengerInteriorType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, PassengerInteriorType::all());
    }

    /**
     * Test user with 'passenger-interior-type-delete' permission can delete a passenger interior type.
     *
     * @return void
     */
    public function testPassengerInteriorTypeCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $passengerInteriorType = PassengerInteriorType::factory()->create();
        $response = $this->delete('api/passenger-interior-types/' . $passengerInteriorType->id);

        $this->assertCount(0, PassengerInteriorType::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
