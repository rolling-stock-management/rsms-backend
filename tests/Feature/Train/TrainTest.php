<?php

namespace Tests\Feature\Train;

use App\Models\Role;
use App\Models\Timetable;
use App\Models\Train;
use App\Models\User;
use Database\Seeders\Permissions\TrainPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TrainTest extends TestCase
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
        $this->seed(TrainPermissionsSeeder::class);
        Timetable::factory()->create();
        $this->data = [
            'number' => '8601',
            'route' => 'Sofia - Plovdiv - Burgas',
            'note' => 'Some text to serve as a note to the train...',
            'timetable_id' => 1
        ];
    }

    /**
     * Test user must be logged in in order to create an train.
     *
     * @return void
     */
    public function testTrainCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/trains', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, Train::all());
    }

    /**
     * Test user must have the 'train-create' permission in order to create an train.
     *
     * @return void
     */
    public function testTrainCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/trains', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Train::all());
    }

    /**
     * Test user with 'train-create' permission can create an train.
     *
     * @return void
     */
    public function testTrainCanBeCreatedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/trains', $this->data);
        $train = Train::first();

        $this->assertCount(1, Train::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $train->id,
                'number' => $train->number,
                'route' => $train->route,
                'note' => $train->note,
                'created_at' => $train->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $train->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test train number and route are required.
     *
     * @return void
     */
    public function testTrainNumberAndRouteAreRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['number', 'route'])
            ->each(function ($field) {
                $response = $this->post('api/trains', array_merge($this->data, [$field => null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test train string fields validation: number, route, note.
     *
     * @return void
     */
    public function testTrainStringFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['number', 'route', 'note'])
            ->each(function ($field) {
                $response = $this->post('api/trains', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'train-viewAny' permission in order to see a list of trains.
     *
     * @return void
     */
    public function testTrainsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/trains');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'train-viewAny' permission can see a list of trains.
     *
     * @return void
     */
    public function testTrainsCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Train::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/trains');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'train-view' permission in order to view an train.
     *
     * @return void
     */
    public function testTrainCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $train = Train::factory()->create();
        $response = $this->get('api/trains/' . $train->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'train-view' permission can view an train.
     *
     * @return void
     */
    public function testTrainCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $train = Train::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/trains/' . $train->id);

        $response->assertJson([
            'data' => [
                'id' => $train->id,
                'number' => $train->number,
                'route' => $train->route,
                'note' => $train->note,
                'created_at' => $train->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $train->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'train-update' permission in order to update an train.
     *
     * @return void
     */
    public function testTrainCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $train = Train::factory()->create();
        $response = $this->patch('api/trains/' . $train->id, $this->data);
        $train = Train::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['number'], $train->number);
    }

    /**
     * Test user with 'train-update' permission can update an train.
     *
     * @return void
     */
    public function testTrainCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $train = Train::factory()->create();
        $response = $this->patch('api/trains/' . $train->id, $this->data);
        $train = Train::first();

        $this->assertEquals($this->data['number'], $train->number);
        $this->assertEquals($this->data['route'], $train->route);
        $this->assertEquals($this->data['note'], $train->note);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $train->id,
                'number' => $train->number,
                'route' => $train->route,
                'note' => $train->note,
                'created_at' => $train->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $train->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'train-delete' permission in order to delete an train.
     *
     * @return void
     */
    public function testTrainCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $train = Train::factory()->create();
        $response = $this->delete('api/trains/' . $train->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Train::all());
    }

    /**
     * Test user with 'train-delete' permission can delete an train.
     *
     * @return void
     */
    public function testTrainCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $train = Train::factory()->create();
        $response = $this->delete('api/trains/' . $train->id);

        $this->assertCount(0, Train::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
