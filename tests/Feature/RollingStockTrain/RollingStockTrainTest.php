<?php

namespace Tests\Feature\RollingStockTrain;

use App\Models\PassengerWagon;
use App\Models\RollingStockTrain;
use App\Models\Role;
use App\Models\Train;
use App\Models\User;
use Database\Seeders\Permissions\RollingStockTrainPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RollingStockTrainTest extends TestCase
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
        $this->seed(RollingStockTrainPermissionsSeeder::class);
        Train::factory()->create();
        PassengerWagon::factory()->create();
        $this->data = [
            'date' => '2021-05-06',
            'position' => 1,
            'train_id' => 1,
            'trainable_type' => 1,
            'trainable_id' => 1,
            'comment' => 'Some text to serve as a comment of the rolling-stock-train...',
        ];
    }

    /**
     * Test user must be logged in in order to create a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/rolling-stock-trains', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, RollingStockTrain::all());
    }

    /**
     * Test user must have the 'rolling-stock-train-create' permission in order to create a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/rolling-stock-trains', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, RollingStockTrain::all());
    }

    /**
     * Test user with 'rolling-stock-train-create' permission can create a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCanBeCreatedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', $this->data);
        $rollingStockTrain = RollingStockTrain::first();

        $this->assertCount(1, RollingStockTrain::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $rollingStockTrain->id,
                'date' => $rollingStockTrain->date->format('Y-m-d'),
                'comment' => $rollingStockTrain->comment,
                'position' => $rollingStockTrain->position,
                'created_at' => $rollingStockTrain->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $rollingStockTrain->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test rolling-stock-train date and position are required.
     *
     * @return void
     */
    public function testRepaiRequiredFields()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['date', 'position'])
            ->each(function ($field) {
                $response = $this->post('api/rolling-stock-trains', array_merge($this->data, [$field => null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test rolling-stock-train comment must be string.
     *
     * @return void
     */
    public function testRollingStockTrainCommentMustBeString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['comment' => (object)null]));
        $response->assertSessionHasErrors('comment');
    }

    /**
     * Test rolling-stock-train date must be a date.
     *
     * @return void
     */
    public function testRollingStockTrainDateMustBeADate()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['date' => 'aaa']));
        $response->assertSessionHasErrors('date');

        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['date' => '1']));
        $response->assertSessionHasErrors('date');
    }

    /**
     * Test user must have the 'rolling-stock-train-viewAny' permission in order to see a list of rolling-stock-trains.
     *
     * @return void
     */
    public function testRollingStockTrainsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/rolling-stock-trains');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'rolling-stock-train-viewAny' permission can see a list of rolling-stock-trains.
     *
     * @return void
     */
    public function testRollingStockTrainsCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        RollingStockTrain::factory()->count(11)->for(
            PassengerWagon::factory(), 'trainable'
        )->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/rolling-stock-trains');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'rolling-stock-train-view' permission in order to view a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $rollingStockTrain = RollingStockTrain::factory()->create();
        $response = $this->get('api/rolling-stock-trains/' . $rollingStockTrain->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'rolling-stock-train-view' permission can view a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $rollingStockTrain = RollingStockTrain::factory()->for(
            PassengerWagon::factory(), 'trainable'
        )->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/rolling-stock-trains/' . $rollingStockTrain->id);

        $response->assertJson([
            'data' => [
                'id' => $rollingStockTrain->id,
                'date' => $rollingStockTrain->date->format('Y-m-d'),
                'comment' => $rollingStockTrain->comment,
                'position' => $rollingStockTrain->position,
                'created_at' => $rollingStockTrain->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $rollingStockTrain->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'rolling-stock-train-update' permission in order to update a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $rollingStockTrain = RollingStockTrain::factory()->create();
        $response = $this->patch('api/rolling-stock-trains/' . $rollingStockTrain->id, $this->data);
        $rollingStockTrain = RollingStockTrain::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['comment'], $rollingStockTrain->comment);
    }

    /**
     * Test user with 'rolling-stock-train-update' permission can update a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $rollingStockTrain = RollingStockTrain::factory()->for(
            PassengerWagon::factory(), 'trainable'
        )->create();
        $response = $this->patch('api/rolling-stock-trains/' . $rollingStockTrain->id, $this->data);
        $rollingStockTrain = RollingStockTrain::first();

        $this->assertEquals($this->data['date'], $rollingStockTrain->date->format('Y-m-d'));
        $this->assertEquals($this->data['comment'], $rollingStockTrain->comment);
        $this->assertEquals($this->data['position'], $rollingStockTrain->position);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $rollingStockTrain->id,
                'date' => $rollingStockTrain->date->format('Y-m-d'),
                'comment' => $rollingStockTrain->comment,
                'position' => $rollingStockTrain->position,
                'created_at' => $rollingStockTrain->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $rollingStockTrain->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'rolling-stock-train-delete' permission in order to delete a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $rollingStockTrain = RollingStockTrain::factory()->create();
        $response = $this->delete('api/rolling-stock-trains/' . $rollingStockTrain->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, RollingStockTrain::all());
    }

    /**
     * Test user with 'rolling-stock-train-delete' permission can delete a rolling-stock-train.
     *
     * @return void
     */
    public function testRollingStockTrainCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $rollingStockTrain = RollingStockTrain::factory()->create();
        $response = $this->delete('api/rolling-stock-trains/' . $rollingStockTrain->id);

        $this->assertCount(0, RollingStockTrain::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
