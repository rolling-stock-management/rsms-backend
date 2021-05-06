<?php

namespace Tests\Feature\RollingStockTrain;

use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\RollingStockTrain;
use App\Models\Role;
use App\Models\TractiveUnit;
use App\Models\Train;
use App\Models\User;
use Database\Seeders\Permissions\RollingStockTrainPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RollingStockTrainRelationshipsTest extends TestCase
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
        FreightWagon::factory()->create();
        TractiveUnit::factory()->create();
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
     * Test rolling stock train train_id must be integer.
     *
     * @return void
     */
    public function testRollingStockTrainTrainIdMustBeInteger()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['train_id' => (object)null]));
        $response->assertSessionHasErrors('train_id');

        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['train_id' => 'aa']));
        $response->assertSessionHasErrors('train_id');
    }

    /**
     * Test rolling stock train train_id must exist.
     *
     * @return void
     */
    public function testRollingStockTrainTrainIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['train_id' => 5]));
        $response->assertSessionHasErrors('train_id');
    }

    /**
     * Test train can be assigned to rolling stock train.
     *
     * @return void
     */
    public function testTrainCanBeAssignedToRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', $this->data);
        RollingStockTrain::first();

        $this->assertCount(1, RollingStockTrain::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['train']);
    }

    /**
     * Test train can be updated on rolling stock train.
     *
     * @return void
     */
    public function testTrainCanBeUpdatedOnRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $rollingStockTrain = RollingStockTrain::factory()->for(
            PassengerWagon::factory(), 'trainable'
        )->create();
        $response = $this->patch('api/rolling-stock-trains/' . $rollingStockTrain->id, array_merge($this->data, ['train_id' => 2]));
        $rollingStockTrain = RollingStockTrain::first();

        $this->assertEquals($this->data['comment'], $rollingStockTrain->comment);
        $this->assertEquals(2, $rollingStockTrain->train->id);
        $response->assertStatus(Response::HTTP_OK);
    }



    /**
     * Test passenger wagon can be assigned to rolling stock train.
     *
     * @return void
     */
    public function testPassengerWagonCanBeAssignedToRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['trainable_type' => 1, 'trainable_id' => 1]));
        RollingStockTrain::first();

        $this->assertCount(1, RollingStockTrain::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['trainable']);
        $this->assertEquals('App\Models\PassengerWagon',$response['data']['trainable_type']);
    }

    /**
     * Test passenger wagon can be updated on rolling stock train.
     *
     * @return void
     */
    public function testPassengerWagonCanBeUpdatedOnRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $rollingStockTrain = RollingStockTrain::factory()->for(
            PassengerWagon::factory(), 'trainable'
        )->create();
        $response = $this->patch('api/rolling-stock-trains/' . $rollingStockTrain->id, array_merge($this->data, ['trainable_type' => 1, 'trainable_id' => 1]));
        $rollingStockTrain = RollingStockTrain::first();

        $this->assertEquals($this->data['comment'], $rollingStockTrain->comment);
        $this->assertEquals(1, $rollingStockTrain->trainable->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('App\Models\PassengerWagon',$response['data']['trainable_type']);
    }

    /**
     * Test passenger_wagon_id must exist.
     *
     * @return void
     */
    public function testPassengerWagonIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['trainable_type' => 1, 'trainable_id' => 5]));
        $response->assertSessionHasErrors('trainable_id');
    }

    /**
     * Test freight wagon can be assigned to rolling stock train.
     *
     * @return void
     */
    public function testFreightWagonCanBeAssignedToRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['trainable_type' => 2, 'trainable_id' => 1]));
        RollingStockTrain::first();

        $this->assertCount(1, RollingStockTrain::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['trainable']);
        $this->assertEquals('App\Models\FreightWagon',$response['data']['trainable_type']);
    }

    /**
     * Test freight wagon can be updated on rolling stock train.
     *
     * @return void
     */
    public function testFreightWagonCanBeUpdatedOnRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $rollingStockTrain = RollingStockTrain::factory()->for(
            FreightWagon::factory(), 'trainable'
        )->create();
        $response = $this->patch('api/rolling-stock-trains/' . $rollingStockTrain->id, array_merge($this->data, ['trainable_type' => 2, 'trainable_id' => 1]));
        $rollingStockTrain = RollingStockTrain::first();

        $this->assertEquals($this->data['comment'], $rollingStockTrain->comment);
        $this->assertEquals(1, $rollingStockTrain->trainable->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('App\Models\FreightWagon',$response['data']['trainable_type']);
    }

    /**
     * Test freight_wagon_id must exist.
     *
     * @return void
     */
    public function testFreightWagonIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['trainable_type' => 2, 'trainable_id' => 5]));
        $response->assertSessionHasErrors('trainable_id');
    }

    /**
     * Test tractive unit can be assigned to rolling stock train.
     *
     * @return void
     */
    public function testTractiveUnitCanBeAssignedToRollingStockTrain()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['trainable_type' => 3, 'trainable_id' => 1]));
        RollingStockTrain::first();

        $this->assertCount(1, RollingStockTrain::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['trainable']);
        $this->assertEquals('App\Models\TractiveUnit',$response['data']['trainable_type']);
    }

    /**
     * Test tractive unit can be updated on rolling stock train.
     *
     * @return void
     */
    public function testTractiveUnitCanBeUpdatedOnRollingStockTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $rollingStockTrain = RollingStockTrain::factory()->for(
            TractiveUnit::factory(), 'trainable'
        )->create();
        $response = $this->patch('api/rolling-stock-trains/' . $rollingStockTrain->id, array_merge($this->data, ['trainable_type' => 3, 'trainable_id' => 1]));
        $rollingStockTrain = RollingStockTrain::first();

        $this->assertEquals($this->data['comment'], $rollingStockTrain->comment);
        $this->assertEquals(1, $rollingStockTrain->trainable->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('App\Models\TractiveUnit',$response['data']['trainable_type']);
    }

    /**
     * Test tractive_unit_id must exist.
     *
     * @return void
     */
    public function testTractiveUnitIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/rolling-stock-trains', array_merge($this->data, ['trainable_type' => 3, 'trainable_id' => 5]));
        $response->assertSessionHasErrors('trainable_id');
    }
}
