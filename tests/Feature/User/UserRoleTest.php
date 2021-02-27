<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\UserPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        Role::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(UserPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync([3, 4]);
        $this->data = [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'seCretTest123!',
            'password_confirmation' => 'seCretTest123!',
            'role_ids' => [1, 2, 3]
        ];
    }

    /**
     * Test roles can be added to user when creating.
     *
     * @return void
     */
    public function testRolesCanBeAddedToUserWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/users', $this->data);
        $user = User::find(2);

        $this->assertCount(2, User::all());
        $this->assertCount(3, $user->roles);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
        $this->assertCount(3, $response['data']['roles']);
    }

    /**
     * Test roles can be added to user when updating.
     *
     * @return void
     */
    public function testRolesCanBeAddedToUserWhenUpdating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $response = $this->patch('api/users/' . $user->id, $this->data);
        $user = User::find(2);

        $this->assertCount(3, $user->roles);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response['data']['roles']);
    }

    /**
     * Test roles can be removed from user when updating.
     *
     * @return void
     */
    public function testRolesCanBeRemovedFromUserWhenUpdating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $user->roles()->sync([1, 2, 3]);

        $response = $this->patch('api/users/' . $user->id, array_merge($this->data, ['role_ids' => [1, 2]]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response['data']['roles']);
    }
}
