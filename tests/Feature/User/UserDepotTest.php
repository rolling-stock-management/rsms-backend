<?php

namespace Tests\Feature\User;

use App\Models\Depot;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\UserPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserDepotTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        Depot::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(UserPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync([3, 4]);
        $this->data = [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'seCretTest123!',
            'password_confirmation' => 'seCretTest123!',
            'depot_id' => 1
        ];
    }

    /**
     * Test depot can be assigned to user when creating.
     *
     * @return void
     */
    public function testDepotCanBeAssignedToUserWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/users', $this->data);
        $user = User::find(2);

        $this->assertCount(2, User::all());
        $this->assertNotNull($user->depot);
        $response->assertStatus(Response::HTTP_CREATED);
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
        $this->assertNotNull($response['data']['depot']);
    }

    /**
     * Test depot can be assigned to user when updating.
     *
     * @return void
     */
    public function testDepotCanBeAssignedToUserWhenUpdating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create();
        $response = $this->patch('api/users/' . $user->id, $this->data);
        $user = User::find(2);

        $this->assertNotNull($user->depot);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotNull($response['data']['depot']);
    }

    /**
     * Test depot can be removed from user when updating.
     *
     * @return void
     */
    public function testDepotCanBeRemovedFromUserWhenUpdating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $user = User::factory()->create(['depot_id' => 1]);

        $response = $this->patch('api/users/' . $user->id, array_merge($this->data, ['depot_id' => null]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($response['data']['depot']);
    }
}
