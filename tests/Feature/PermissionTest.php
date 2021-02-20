<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->get('api/permissions');
        $this->fail('Test not implemented.'); //TODO: Add test and logic.
    }

    /**
     * Test a list of permissions can be retrieved by user with the right permission.
     *
     * @return void
     */
    public function testPermissionsListCanBeRetrieved()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        Permission::factory()->count(3)->create();
        $response = $this->get('api/permissions');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(3, 'data');
    }
}
