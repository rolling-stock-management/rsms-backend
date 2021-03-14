<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RolePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RolePermissionTest extends TestCase
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
        $this->user->roles[0]->permissions()->sync([3, 4]);
        $this->data = [
            'name' => 'role-name',
            'permission_ids' => [1, 2, 3]
        ];
    }
    /**
     * Test role permissions can be added to role when creating.
     *
     * @return void
     */
    public function testPermissionsCanBeAddedToRoleWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/roles', $this->data);
        $role = Role::find(2);

        $this->assertCount(2, Role::all());
        $this->assertCount(3, $role->permissions);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
            ]
        ]);
        $this->assertCount(3, $response['data']['permissions']);
    }

    /**
     * Test role permissions can be added to role when updating.
     *
     * @return void
     */
    public function testPermissionsCanBeAddedToRoleWhenUpdating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $role = Role::factory()->create();
        $response = $this->patch('api/roles/' . $role->id, $this->data);
        $role = Role::find(2);

        $this->assertCount(3, $role->permissions);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response['data']['permissions']);
    }

    /**
     * Test role permissions can be removed from role when updating.
     *
     * @return void
     */
    public function testPermissionsCanBeRemovedFromRoleWhenUpdating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $role = Role::factory()->create();
        $role->permissions()->sync([1, 2, 3]);

        $response = $this->patch('api/roles/' . $role->id, array_merge($this->data, ['permission_ids' => [1, 2]]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response['data']['permissions']);
    }

    /**
     * Test permission_ids must be an array.
     *
     * @return void
     */
    public function testPermissionIdsMustBeAnArray()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/roles', array_merge($this->data, ['permission_ids' => (object)null]));

        $response->assertSessionHasErrors(['permission_ids']);
    }
}
