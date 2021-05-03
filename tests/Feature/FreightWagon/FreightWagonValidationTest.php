<?php

namespace Tests\Feature\FreightWagon;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\FreightWagon;
use App\Models\FreightWagonType;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\Permissions\FreightWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FreightWagonValidationTest extends TestCase
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
        $this->user->roles[0]->permissions()->sync(3);
        FreightWagonType::factory()->create(['name'=>'F']);
        Owner::factory()->create();
        Status::factory()->create();
        RepairWorkshop::factory()->create();
        Depot::factory()->create();
        $this->data = [
            'number' => '845266510708',
            'type_id' => 1,
            'letter_marking' => 'Fals',
            'tare' => 24.950,
            'weight_capacity' => 50,
            'length_capacity' => 10,
            'volume_capacity' => 75,
            'area_capacity' => 30,
            'max_speed' => 100,
            'length' => 12.79,
            'brake_marking' => 'KE-GP',
            'owner_id' => 1,
            'status_id' => 1,
            'repair_date' => '2021-01-01',
            'repair_valid_until' => '2022-01-01',
            'repair_workshop_id' => 1,
            'depot_id' => 1,
            'other_info' => 'S:A - 39t, B1 - 39t, B2 - 47t, C - 55t',
        ];
    }

    /**
     * Test freight wagon number must be only digits.
     *
     * @return void
     */
    public function testFreightWagonNumberMustBeOnlyDigits()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        //Digits + space
        $response = $this->post('api/freight-wagons', array_merge($this->data, ['number' => '61 285970039']));
        $response->assertSessionHasErrors('number');

        //Digits + letter
        $response = $this->post('api/freight-wagons', array_merge($this->data, ['number' => '61a285970039']));
        $response->assertSessionHasErrors('number');

        //Only letters
        $response = $this->post('api/freight-wagons', array_merge($this->data, ['number' => 'aaaaaaaaaaaa']));
        $response->assertSessionHasErrors('number');
    }

    /**
     * Test freight wagon number must be exactly 12 digits.
     *
     * @return void
     */
    public function testFreightWagonNumberMustBeExactly12Digits()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        //More digits
        $response = $this->post('api/freight-wagons', array_merge($this->data, ['number' => '6152859700391']));
        $response->assertSessionHasErrors('number');

        //Less digits
        $response = $this->post('api/freight-wagons', array_merge($this->data, ['number' => '61528597003']));
        $response->assertSessionHasErrors('number');
    }

    /**
     * Test freight wagon number must be unique.
     *
     * @return void
     */
    public function testFreightWagonNumberMustBeUnique()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        FreightWagon::factory()->create(['number' => '515219401400']);

        $response = $this->post('api/freight-wagons', array_merge($this->data, ['number' => '515219401400']));

        $response->assertSessionHasErrors('number');
    }

    /**
     * Test freight wagon max_speed must be an integer.
     *
     * @return void
     */
    public function testFreightWagonMaxSpeedMustBeInteger()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/freight-wagons', array_merge($this->data, ['max_speed' => (object)null]));
        $response->assertSessionHasErrors('max_speed');

        $response = $this->post('api/freight-wagons', array_merge($this->data, ['max_speed' => 'aaa']));
        $response->assertSessionHasErrors('max_speed');
    }

    /**
     * Test freight wagon decimal fields validation: tare, weight_capacity, length_capacity, volume_capacity, area_capacity, length
     *
     * @return void
     */
    public function testFreightWagonDecimalFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['tare', 'weight_capacity', 'length_capacity', 'volume_capacity', 'area_capacity', 'length'])
            ->each(function ($field) {
                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test freight wagon string fields validation: letter_marking, brake_marking, other_info
     *
     * @return void
     */
    public function testFreightWagonStringFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['letter_marking', 'brake_marking', 'other_info'])
            ->each(function ($field) {
                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test freight wagon repair_date must be a date.
     *
     * @return void
     */
    public function testFreightWagonRepairDateMustBeADate()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['repair_date', 'repair_valid_until'])
            ->each(function ($field) {
                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/freight-wagons', array_merge($this->data, [$field => '123']));
                $response->assertSessionHasErrors($field);
            });
    }
}
