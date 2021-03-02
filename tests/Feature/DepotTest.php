<?php

namespace Tests\Feature;

use App\Models\Depot;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\DepotPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DepotTest extends TestCase
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
        $this->seed(DepotPermissionSeeder::class);
        $this->data = [
            'name' => 'depot1',
            'note' => 'Some text to serve as a note to the depot...'
        ];
    }

    /**
     * Test user must be logged in in order to create a depot.
     *
     * @return void
     */
    public function testDepotCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/depots', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, Depot::all());
    }

    /**
     * Test user must have the 'depot-create' permission in order to create a depot.
     *
     * @return void
     */
    public function testDepotCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/depots', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Depot::all());
    }

    /**
     * Test user with 'depot-create' permission can create a depot.
     *
     * @return void
     */
    public function testDepotCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/depots', $this->data);
        $depot = Depot::first();

        $this->assertCount(1, Depot::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $depot->id,
                'name' => $depot->name,
                'note' => $depot->note
            ]
        ]);
    }

    /**
     * Test depot name is required.
     *
     * @return void
     */
    public function testDepotNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/depots', array_merge($this->data, ['name' => null]));

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test depot name and note must be strings.
     *
     * @return void
     */
    public function testDepotNameAndNoteMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'note'])
            ->each(function ($field) {
                $response = $this->post('api/depots', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'depot-viewAny' permission in order to see a list of depots.
     *
     * @return void
     */
    public function testDepotsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/depots');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'depot-viewAny' permission can see a list of depots.
     *
     * @return void
     */
    public function testDepotsCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Depot::factory()->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/depots');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test depot no-pagination option.
     *
     * @return void
     */
    public function testDepotPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Depot::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/depots?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'depot-view' permission in order to view a depot.
     *
     * @return void
     */
    public function testDepotCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $depot = Depot::factory()->create();
        $response = $this->get('api/depots/' . $depot->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'depot-view' permission can view a depot.
     *
     * @return void
     */
    public function testDepotCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $depot = Depot::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/depots/' . $depot->id);

        $response->assertJson([
            'data' => [
                'id' => $depot->id,
                'name' => $depot->name,
                'note' => $depot->note,
                'last_updated' => $depot->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'depot-update' permission in order to update a depot.
     *
     * @return void
     */
    public function testDepotCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $depot = Depot::factory()->create();
        $response = $this->patch('api/depots/' . $depot->id, $this->data);
        $depot = Depot::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $depot->name);
    }

    /**
     * Test user with 'depot-update' permission can update a depot.
     *
     * @return void
     */
    public function testDepotCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $depot = Depot::factory()->create();
        $response = $this->patch('api/depots/' . $depot->id, $this->data);
        $depot = Depot::first();

        $this->assertEquals($this->data['name'], $depot->name);
        $this->assertEquals($this->data['note'], $depot->note);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $depot->id,
                'name' => $depot->name,
                'note' => $depot->note
            ]
        ]);
    }

    /**
     * Test user must have the 'depot-delete' permission in order to delete a depot.
     *
     * @return void
     */
    public function testDepotCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $depot = Depot::factory()->create();
        $response = $this->delete('api/depots/' . $depot->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Depot::all());
    }

    /**
     * Test user with 'depot-delete' permission can delete a depot.
     *
     * @return void
     */
    public function testDepotCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $depot = Depot::factory()->create();
        $response = $this->delete('api/depots/' . $depot->id);

        $this->assertCount(0, Depot::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
