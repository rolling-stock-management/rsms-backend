<?php

namespace Tests\Feature\TractiveUnit;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\Status;
use App\Models\TractiveUnit;
use App\Models\User;
use Database\Seeders\Permissions\TractiveUnitPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TractiveUnitValidationTest extends TestCase
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
        $this->seed(TractiveUnitPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync(3);
        Owner::factory()->create();
        Status::factory()->create();
        RepairWorkshop::factory()->create();
        Depot::factory()->create();
        $this->data = [
            'number' => '915200441003',
            'max_speed' => 130,
            'power_output' => 3140,
            'tractive_effort' => 294,
            'weight' => 87,
            'axle_arrangement' => 'Во′-Во′',
            'length' => 16.5,
            'brake_marking' => 'KE-GPR+E',
            'owner_id' => 1,
            'status_id' => 1,
            'repair_date' => '2021-01-01',
            'repair_valid_until' => '2022-01-01',
            'repair_workshop_id' => 1,
            'depot_id' => 1,
            'other_info' => 'R - 97t, P - 58t, G - 58t',
        ];
    }

    /**
     * Test tractive unit number must be only digits.
     *
     * @return void
     */
    public function testTractiveUnitNumberMustBeOnlyDigits()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        //Digits + space
        $response = $this->post('api/tractive-units', array_merge($this->data, ['number' => '61 285970039']));
        $response->assertSessionHasErrors('number');

        //Digits + letter
        $response = $this->post('api/tractive-units', array_merge($this->data, ['number' => '61a285970039']));
        $response->assertSessionHasErrors('number');

        //Only letters
        $response = $this->post('api/tractive-units', array_merge($this->data, ['number' => 'aaaaaaaaaaaa']));
        $response->assertSessionHasErrors('number');
    }

    /**
     * Test tractive unit number must be exactly 12 digits.
     *
     * @return void
     */
    public function testTractiveUnitNumberMustBeExactly12Digits()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        //More digits
        $response = $this->post('api/tractive-units', array_merge($this->data, ['number' => '6152859700391']));
        $response->assertSessionHasErrors('number');

        //Less digits
        $response = $this->post('api/tractive-units', array_merge($this->data, ['number' => '61528597003']));
        $response->assertSessionHasErrors('number');
    }

    /**
     * Test tractive unit number must be unique.
     *
     * @return void
     */
    public function testTractiveUnitNumberMustBeUnique()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        TractiveUnit::factory()->create(['number' => '615285970039']);

        $response = $this->post('api/tractive-units', array_merge($this->data, ['number' => '615285970039']));

        $response->assertSessionHasErrors('number');
    }

    /**
     * Test tractive unit integer fields validation: max_speed, power_output, tractive_effort, weight.
     *
     * @return void
     */
    public function testTractiveUnitIntegerFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['max_speed', 'power_output', 'tractive_effort', 'weight'])
            ->each(function ($field) {
                $response = $this->post('api/tractive-units', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/tractive-units', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test tractive unit length must be a decimal value.
     *
     * @return void
     */
    public function testTractiveUnitLengthMustBeADecimalValue()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/tractive-units', array_merge($this->data, ['length' => (object)null]));
        $response->assertSessionHasErrors('length');

        $response = $this->post('api/tractive-units', array_merge($this->data, ['length' => 'aaa']));
        $response->assertSessionHasErrors('length');
    }

    /**
     * Test tractive unit string fields validation: axle_arrangement, brake_marking, other_info
     *
     * @return void
     */
    public function testTractiveUnitStringFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['axle_arrangement', 'brake_marking', 'other_info'])
            ->each(function ($field) {
                $response = $this->post('api/tractive-units', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test tractive unit date fields validation: repair_date, repair_valid_until
     *
     * @return void
     */
    public function testTractiveUnitDateFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['repair_date', 'repair_valid_until'])
            ->each(function ($field) {
                $response = $this->post('api/tractive-units', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/tractive-units', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/tractive-units', array_merge($this->data, [$field => '123']));
                $response->assertSessionHasErrors($field);
            });
    }
}
