<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\PermissionPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(PermissionPermissionsSeeder::class);
    }

    /**
     * Test user must be logged in in order to see a list of the permissions.
     *
     * @return void
     */
    public function testRedirectToLoginIfUserIsNotAuthenticated()
    {
        $response = $this->get('api/permissions');
        $response->assertRedirect('api/login');
    }

    /**
     * Test user must have the 'permission-viewAny' permission in order to see a list of the permissions.
     *
     * @return void
     */
    public function testUserWithoutPermissionCannotGetPermissionsList()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/permissions');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test a list of permissions can be retrieved by user with the right permission.
     *
     * @return void
     */
    public function testPermissionsListCanBeRetrieved()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        Permission::factory()->count(3)->create();
        $response = $this->get('api/permissions');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(4, 'data');
    }
}
