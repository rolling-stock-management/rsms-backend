<?php

namespace Tests\Feature;

use App\Models\FreightWagonType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\FreightWagonTypePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FreightWagonTypeTest extends TestCase
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
        $this->seed(FreightWagonTypePermissionsSeeder::class);
        $this->data = [
            'name' => 'freightWagonType1',
            'description' => 'Some text to serve as a description to the freight wagon type...',
        ];
    }

    /**
     * Test user must be logged in in order to create a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/freight-wagon-types', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, FreightWagonType::all());
    }

    /**
     * Test user must have the 'freight-wagon-type-create' permission in order to create a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/freight-wagon-types', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, FreightWagonType::all());
    }

    /**
     * Test user with 'freight-wagon-type-create' permission can create a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/freight-wagon-types', $this->data);
        $freightWagonType = FreightWagonType::first();

        $this->assertCount(1, FreightWagonType::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $freightWagonType->id,
                'name' => $freightWagonType->name,
                'description' => $freightWagonType->description,
                'created_at' => $freightWagonType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $freightWagonType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test freight wagon type name is required.
     *
     * @return void
     */
    public function testFreightWagonTypeNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/freight-wagon-types', array_merge($this->data, ['name' => null]));
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test freight wagon type name and description must be of correct type.
     *
     * @return void
     */
    public function testFreightWagonTypeNameAndDescriptionMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'description'])
            ->each(function ($field) {
                $response = $this->post('api/freight-wagon-types', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'freight-wagon-type-viewAny' permission in order to see a list of freight wagon types.
     *
     * @return void
     */
    public function testFreightWagonTypesCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagon-types');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'freight-wagon-type-viewAny' permission can see a list of freight wagon types.
     *
     * @return void
     */
    public function testFreightWagonTypesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        FreightWagonType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/freight-wagon-types');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test freight wagon type no-pagination option.
     *
     * @return void
     */
    public function testFreightWagonTypesPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        FreightWagonType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/freight-wagon-types?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'freight-wagon-type-view' permission in order to view a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagonType = FreightWagonType::factory()->create();
        $response = $this->get('api/freight-wagon-types/' . $freightWagonType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'freight-wagon-type-view' permission can view a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagonType = FreightWagonType::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/freight-wagon-types/' . $freightWagonType->id);

        $response->assertJson([
            'data' => [
                'id' => $freightWagonType->id,
                'name' => $freightWagonType->name,
                'description' => $freightWagonType->description,
                'created_at' => $freightWagonType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $freightWagonType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'freight-wagon-type-update' permission in order to update a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagonType = FreightWagonType::factory()->create();
        $response = $this->patch('api/freight-wagon-types/' . $freightWagonType->id, $this->data);
        $freightWagonType = FreightWagonType::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $freightWagonType->name);
    }

    /**
     * Test user with 'freight-wagon-type-update' permission can update a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $freightWagonType = FreightWagonType::factory()->create();
        $response = $this->patch('api/freight-wagon-types/' . $freightWagonType->id, $this->data);
        $freightWagonType = FreightWagonType::first();

        $this->assertEquals($this->data['name'], $freightWagonType->name);
        $this->assertEquals($this->data['description'], $freightWagonType->description);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $freightWagonType->id,
                'name' => $freightWagonType->name,
                'description' => $freightWagonType->description,
                'created_at' => $freightWagonType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $freightWagonType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'freight-wagon-type-delete' permission in order to delete a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $freightWagonType = FreightWagonType::factory()->create();
        $response = $this->delete('api/freight-wagon-types/' . $freightWagonType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, FreightWagonType::all());
    }

    /**
     * Test user with 'freight-wagon-type-delete' permission can delete a freight wagon type.
     *
     * @return void
     */
    public function testFreightWagonTypeCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $freightWagonType = FreightWagonType::factory()->create();
        $response = $this->delete('api/freight-wagon-types/' . $freightWagonType->id);

        $this->assertCount(0, FreightWagonType::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
