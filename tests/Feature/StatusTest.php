<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\Permissions\StatusPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StatusTest extends TestCase
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
        $this->seed(StatusPermissionsSeeder::class);
        $this->data = [
            'name' => 'status1'
        ];
    }

    /**
     * Test user must be logged in in order to create a status.
     *
     * @return void
     */
    public function testStatusCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/statuses', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, Status::all());
    }

    /**
     * Test user must have the 'status-create' permission in order to create a status.
     *
     * @return void
     */
    public function testStatusCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/statuses', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Status::all());
    }

    /**
     * Test user with 'status-create' permission can create a status.
     *
     * @return void
     */
    public function testStatusCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/statuses', $this->data);
        $status = Status::first();

        $this->assertCount(1, Status::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $status->id,
                'name' => $status->name,
                'created_at' => $status->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $status->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test status name is required.
     *
     * @return void
     */
    public function testStatusNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/statuses', array_merge($this->data, ['name' => null]));
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test status name must be a string.
     *
     * @return void
     */
    public function testStatusNameMustBeString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/statuses', array_merge($this->data, ['name' => (object)null]));
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test user must have the 'status-viewAny' permission in order to see a list of statuss.
     *
     * @return void
     */
    public function testStatussCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/statuses');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'status-viewAny' permission can see a list of statuss.
     *
     * @return void
     */
    public function testStatusesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Status::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/statuses');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test status no-pagination option.
     *
     * @return void
     */
    public function testStatusesPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Status::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/statuses?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'status-view' permission in order to view a status.
     *
     * @return void
     */
    public function testStatusCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $status = Status::factory()->create();
        $response = $this->get('api/statuses/' . $status->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'status-view' permission can view a status.
     *
     * @return void
     */
    public function testStatusCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $status = Status::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/statuses/' . $status->id);

        $response->assertJson([
            'data' => [
                'id' => $status->id,
                'name' => $status->name,
                'created_at' => $status->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $status->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'status-update' permission in order to update a status.
     *
     * @return void
     */
    public function testStatusCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $status = Status::factory()->create();
        $response = $this->patch('api/statuses/' . $status->id, $this->data);
        $status = Status::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $status->name);
    }

    /**
     * Test user with 'status-update' permission can update a status.
     *
     * @return void
     */
    public function testStatusCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $status = Status::factory()->create();
        $response = $this->patch('api/statuses/' . $status->id, $this->data);
        $status = Status::first();

        $this->assertEquals($this->data['name'], $status->name);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $status->id,
                'name' => $status->name,
                'created_at' => $status->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $status->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'status-delete' permission in order to delete a status.
     *
     * @return void
     */
    public function testStatusCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $status = Status::factory()->create();
        $response = $this->delete('api/statuses/' . $status->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Status::all());
    }

    /**
     * Test user with 'status-delete' permission can delete a status.
     *
     * @return void
     */
    public function testStatusCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $status = Status::factory()->create();
        $response = $this->delete('api/statuses/' . $status->id);

        $this->assertCount(0, Status::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
