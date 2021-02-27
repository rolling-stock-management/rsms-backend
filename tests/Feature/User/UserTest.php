<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\UserPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
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
        $this->seed(UserPermissionsSeeder::class);
        $this->data = [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'seCretTest123!',
            'password_confirmation' => 'seCretTest123!'
        ];
    }

    /**
     * Test user must be logged in in order to create a user.
     *
     * @return void
     */
    public function testUserCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/users', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(1, User::all());
    }

    /**
     * Test user must have the 'user-create' permission in order to create a user.
     *
     * @return void
     */
    public function testUserCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/users', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, User::all());
    }

    /**
     * Test user with 'user-create' permission can create a user.
     *
     * @return void
     */
    public function testUserCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/users', $this->data);
        $user = User::find(2);

        $this->assertCount(2, User::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
    }

    /**
     * Test user name, email and password are required.
     *
     * @return void
     */
    public function testUserDataIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'email', 'password'])
            ->each(function ($field) {
                $response = $this->post('api/users', array_merge($this->data, [$field => '']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user name, email and password must be strings.
     *
     * @return void
     */
    public function testUserDataMustBeString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'email', 'password'])
            ->each(function ($field) {
                $response = $this->post('api/users', array_merge($this->data, [$field => '']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'user-viewAny' permission in order to see a list of users.
     *
     * @return void
     */
    public function testUsersCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/users');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'user-viewAny' permission can see a list of users.
     *
     * @return void
     */
    public function testUsersCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        User::factory()->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/users');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test user must have the 'user-view' permission in order to retrieve a user.
     *
     * @return void
     */
    public function testUserCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $response = $this->get('api/users/' . $user->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'user-view' permission can view a user.
     *
     * @return void
     */
    public function testUserCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/users/' . $user->id);

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'verified_email' => $user->email_verified_at ? true : false,
                'created_at' => $user->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $user->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'user-update' permission in order to update a user.
     *
     * @return void
     */
    public function testUserCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $response = $this->patch('api/users/' . $user->id, $this->data);
        $user = User::find(2);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $user->name);
    }

    /**
     * Test user with 'user-update' permission can update a user.
     *
     * @return void
     */
    public function testUserCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $user = User::factory()->create();
        $response = $this->patch('api/users/' . $user->id, $this->data);
        $user = User::find(2);

        $this->assertEquals($this->data['name'], $user->name);
        $this->assertEquals($this->data['email'], $user->email);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'verified_email' => $user->email_verified_at ? true : false,
                'created_at' => $user->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $user->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'user-delete' permission in order to delete a user.
     *
     * @return void
     */
    public function testUserCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $response = $this->delete('api/users/' . $user->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(2, User::all());
    }

    /**
     * Test user with 'user-delete' permission can delete a user.
     *
     * @return void
     */
    public function testUserCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $user = User::factory()->create();
        $response = $this->delete('api/users/' . $user->id);

        $this->assertCount(1, User::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
