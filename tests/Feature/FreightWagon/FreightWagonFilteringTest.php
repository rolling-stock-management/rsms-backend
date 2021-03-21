<?php

namespace Tests\Feature\FreightWagon;

use App\Models\FreightWagon;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\FreightWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FreightWagonFilteringTest extends TestCase
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
        $this->seed(FreightWagonPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync(1);
        FreightWagon::factory()->create(['number' => '335249560020']);
        FreightWagon::factory()->create(['number' => '845266510708']);
        FreightWagon::factory()->create(['number' => '335249564061']);
    }

    /**
     * Test freight wagons can be filtered by depot id.
     *
     * @return void
     */
    public function testFreightWagonsCanBeFilteredByDepotId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagons' . '?depot_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test freight wagons can be filtered by repair valid until date this month.
     *
     * @return void
     */
    public function testFreightWagonsCanBeFilteredByRepairValidUntilThisMonth()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        FreightWagon::factory()->create(['repair_valid_until' => date('Y-m-d')]);
        FreightWagon::factory()->create(['repair_valid_until' => date('Y-m-d')]);

        $response = $this->get('api/freight-wagons' . '?repair_valid_until_this_month=1');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test freight wagons can be filtered by repair workshop id.
     *
     * @return void
     */
    public function testFreightCanBeFilteredByRepairWorkshopId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagons' . '?repair_workshop_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test freight wagons can be filtered by status id.
     *
     * @return void
     */
    public function testFreightWagonsCanBeFilteredByStatusId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagons' . '?status_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test freight wagons sorting can be changed.
     *
     * @return void
     */
    public function testFreightWagonsCanBeSorted()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $responseAsc = $this->get('api/freight-wagons' . '?sort=asc');
        $responseDesc = $this->get('api/freight-wagons' . '?sort=desc');

        $this->assertEquals('335249560020', $responseAsc->getData()->data[0]->data->number);
        $this->assertEquals('845266510708', $responseDesc->getData()->data[0]->data->number);
    }

    /**
     * Test freight wagons can be filtered by type id.
     *
     * @return void
     */
    public function testFreightWagonsCanBeFilteredByTypeId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagons' . '?type_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test freight wagons can be filtered by owner id.
     *
     * @return void
     */
    public function testFreightWagonsCanBeFilteredByOwnerId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/freight-wagons' . '?owner_id=2');

        $response->assertJsonCount(1, 'data');
    }
}
