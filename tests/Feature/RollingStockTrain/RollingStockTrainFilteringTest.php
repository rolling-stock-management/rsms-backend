<?php

namespace Tests\Feature\RollingStockTrain;

use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\RollingStockTrain;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RollingStockTrainPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RollingStockTrainFilteringTest extends TestCase
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
    }

    /**
     * Test rolling stock trains  can be filtered by trainable type.
     *
     * @return void
     */
    public function testRollingStockTrainsCanBeFilteredByTrainableType()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        RollingStockTrain::factory()->for(
            FreightWagon::factory(), 'trainable'
        )->create();
        RollingStockTrain::factory()->count(2)->for(
            PassengerWagon::factory(), 'trainable'
        )->create();


        $response = $this->get('api/rolling-stock-trains' . '?trainable_type=1');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test rolling stock trains  cannot be filtered by trainable type with invalid id.
     *
     * @return void
     */
    public function testRollingStockTrainsCannotBeFilteredByTrainableTypeWithInvalidId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        RollingStockTrain::factory()->for(
            FreightWagon::factory(), 'trainable'
        )->create();
        RollingStockTrain::factory()->count(2)->for(
            PassengerWagon::factory(), 'trainable'
        )->create();


        $response = $this->get('api/rolling-stock-trains' . '?trainable_type=10');

        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test rolling stock trains  can be filtered by train id.
     *
     * @return void
     */
    public function testRollingStockTrainsCanBeFilteredByTrainId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        RollingStockTrain::factory()->count(2)->for(
            PassengerWagon::factory(), 'trainable'
        )->create();

        $response = $this->get('api/rolling-stock-trains' . '?train_id=1');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test rolling stock trains can be filtered by date
     *
     * @return void
     */
    public function testRollingStockTrainsCanBeFilteredByDate()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([1]);

        RollingStockTrain::factory()->count(2)->for(
            PassengerWagon::factory(), 'trainable'
        )->create(['date' => '2021-03-02']);
        RollingStockTrain::factory()->for(
            PassengerWagon::factory(), 'trainable'
        )->create(['date' => '2021-05-02']);

        $response = $this->get('api/rolling-stock-trains' . '?date=2021-03-02');

        $response->assertJsonCount(2, 'data');
    }
}
