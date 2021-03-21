<?php

namespace Tests\Feature\PassengerWagon;

use App\Models\PassengerWagon;
use App\Models\PassengerWagonType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\PassengerWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PassengerWagonFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(PassengerWagonPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync(1);
        PassengerWagonType::factory()->create(['name'=>'19-40']);
        PassengerWagonType::factory()->create(['name'=>'22-97']);
        PassengerWagon::factory()->create(['number' => '505219401412']);
        PassengerWagon::factory()->create(['number' => '505219401400']);
        PassengerWagon::factory()->create(['number' => '515222970020']);
    }

    /**
     * Test passenger wagons can be filtered by depot id.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeFilteredByDepotId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagons' . '?depot_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test passenger wagons can be filtered by repair valid until date this month.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeFilteredByRepairValidUntilThisMonth()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerWagonType::factory()->create([
            'name'=> '21-43',
            'repair_valid_for' => 1
        ]);
        PassengerWagon::factory()->create([
            'number' => '505221430012',
            'repair_date' => date("Y-m-d",strtotime("-1 year"))
        ]);
        PassengerWagon::factory()->create([
            'number' => '505221430023',
            'repair_date' => date("Y-m-d",strtotime("-1 year"))
        ]);

        $response = $this->get('api/passenger-wagons' . '?repair_valid_until_this_month=1');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test passenger wagons can be filtered by repair workshop id.
     *
     * @return void
     */
    public function testPassengerCanBeFilteredByRepairWorkshopId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagons' . '?repair_workshop_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test passenger wagons can be filtered by status id.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeFilteredByStatusId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagons' . '?status_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test passenger wagons sorting can be changed.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeSorted()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $responseAsc = $this->get('api/passenger-wagons' . '?sort=asc');
        $responseDesc = $this->get('api/passenger-wagons' . '?sort=desc');

        $this->assertEquals('505219401400', $responseAsc->getData()->data[0]->data->number);
        $this->assertEquals('515222970020', $responseDesc->getData()->data[0]->data->number);
    }

    /**
     * Test passenger wagons can be filtered by type id.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeFilteredByTypeId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagons' . '?type_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test passenger wagons can be filtered by owner id.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeFilteredByOwnerId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-wagons' . '?owner_id=2');

        $response->assertJsonCount(1, 'data');
    }
}
