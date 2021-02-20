<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RolePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleTest extends TestCase
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
        $this->seed(RolePermissionsSeeder::class);
        $this->data = [
            'name' => 'role-name',
        ];
    }

    /**
     * Test user must be logged in in order to create a role.
     *
     * @return void
     */
    public function testRoleCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/roles', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(1, Role::all());
    }

    /**
     * Test user must have the 'role-create' permission in order to create a role.
     *
     * @return void
     */
    public function testRoleCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/roles', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Role::all());
    }

    /**
     * Test user with 'role-create' permission can create a role.
     *
     * @return void
     */
    public function testRoleCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/roles', $this->data);
        $role = Role::find(2);

        $this->assertCount(2, Role::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
            ]
        ]);
    }

    /**
     * Test role name is required.
     *
     * @return void
     */
    public function testRoleNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/roles', array_merge($this->data, ['name' => null]));

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test role name must be a string.
     *
     * @return void
     */
    public function testRoleNameMustBeAString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/roles', array_merge($this->data, ['name' => (object)null]));

        $response->assertSessionHasErrors('name');
    }
    /**
     * Test user must have the 'role-viewAny' permission in order to see a list of roles.
     *
     * @return void
     */
    public function testRolesCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/roles');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'role-viewAny' permission can see a list of roles.
     *
     * @return void
     */
    public function testRolesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Role::factory()->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/roles');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test user must have the 'role-view' permission in order to retrieve a role.
     *
     * @return void
     */
    public function testRoleCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $role = Role::factory()->create();
        $response = $this->get('api/roles/' . $role->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'role-view' permission can update a role.
     *
     * @return void
     */
    public function testRoleCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $role = Role::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/roles/' . $role->id);

        $response->assertJson([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'last_updated' => $role->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'role-update' permission in order to update a role.
     *
     * @return void
     */
    public function testRoleCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $role = Role::factory()->create();
        $response = $this->patch('api/roles/' . $role->id, $this->data);
        $role = Role::find(2);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $role->name);
    }

    /**
     * Test user with 'role-update' permission can update a role.
     *
     * @return void
     */
    public function testRoleCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $role = Role::factory()->create();
        $response = $this->patch('api/roles/' . $role->id, $this->data);
        $role = Role::find(2);

        $this->assertEquals($this->data['name'], $role->name);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $role->id,
                'name' => $role->name
            ]
        ]);
    }

    /**
     * Test user must have the 'role-delete' permission in order to delete a role.
     *
     * @return void
     */
    public function testRoleCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $role = Role::factory()->create();
        $response = $this->delete('api/roles/' . $role->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(2, Role::all());
    }

    /**
     * Test user with 'role-delete' permission can delete a role.
     *
     * @return void
     */
    public function testRoleCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $role = Role::factory()->create();
        $response = $this->delete('api/roles/' . $role->id);

        $this->assertCount(1, Role::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
