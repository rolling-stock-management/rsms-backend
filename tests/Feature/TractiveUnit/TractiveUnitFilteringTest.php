<?php

namespace Tests\Feature\TractiveUnit;

use App\Models\Role;
use App\Models\TractiveUnit;
use App\Models\User;
use Database\Seeders\Permissions\TractiveUnitPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TractiveUnitFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(TractiveUnitPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync(1);
        TractiveUnit::factory()->create(['number' => '915200435393']);
        TractiveUnit::factory()->create(['number' => '915200440989']);
        TractiveUnit::factory()->create(['number' => '915200440625']);
    }

    /**
     * Test tractive units can be filtered by depot id.
     *
     * @return void
     */
    public function testTractiveUnitsCanBeFilteredByDepotId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/tractive-units' . '?depot_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test tractive units can be filtered by repair valid until date this month.
     *
     * @return void
     */
    public function testTractiveUnitsCanBeFilteredByRepairValidUntilThisMonth()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        TractiveUnit::factory()->create(['repair_valid_until' => date('Y-m-d')]);
        TractiveUnit::factory()->create(['repair_valid_until' => date('Y-m-d')]);

        $response = $this->get('api/tractive-units' . '?repair_valid_until_this_month=1');

        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test tractive units can be filtered by repair workshop id.
     *
     * @return void
     */
    public function testTractiveCanBeFilteredByRepairWorkshopId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/tractive-units' . '?repair_workshop_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test tractive units can be filtered by status id.
     *
     * @return void
     */
    public function testTractiveUnitsCanBeFilteredByStatusId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/tractive-units' . '?status_id=2');

        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test tractive units sorting can be changed.
     *
     * @return void
     */
    public function testTractiveUnitsCanBeSorted()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $responseAsc = $this->get('api/tractive-units' . '?sort=asc');
        $responseDesc = $this->get('api/tractive-units' . '?sort=desc');

        $this->assertEquals('915200435393', $responseAsc->getData()->data[0]->data->number);
        $this->assertEquals('915200440989', $responseDesc->getData()->data[0]->data->number);
    }

    /**
     * Test tractive units can be filtered by owner id.
     *
     * @return void
     */
    public function testTractiveUnitsCanBeFilteredByOwnerId()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/tractive-units' . '?owner_id=2');

        $response->assertJsonCount(1, 'data');
    }
}
