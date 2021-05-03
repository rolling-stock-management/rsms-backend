<?php

namespace Tests\Feature\PassengerWagon;

use App\Models\Depot;
use App\Models\Owner;
use App\Models\PassengerWagonType;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\Status;
use App\Models\PassengerWagon;
use App\Models\User;
use Database\Seeders\Permissions\PassengerWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PassengerWagonValidationTest extends TestCase
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
        $this->seed(PassengerWagonPermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync(3);
        PassengerWagonType::factory()->create(['name' => '19-40']);
        Owner::factory()->create();
        Status::factory()->create();
        RepairWorkshop::factory()->create();
        Depot::factory()->create();
        $this->data = [
            'number' => '515219401400',
            'letter_marking' => 'Ame',
            'tare' => 38,
            'total_weight' => 42,
            'seats_count' => 54,
            'max_speed' => 140,
            'length' => 24.5,
            'brake_marking' => 'KE-GPR',
            'owner_id' => 1,
            'status_id' => 1,
            'repair_date' => '2021-01-01',
            'repair_workshop_id' => 1,
            'depot_id' => 1,
            'other_info' => 'R - 60t, P - 42t, G - 40t',
        ];
    }

    /**
     * Test passenger wagon number must be only digits.
     *
     * @return void
     */
    public function testPassengerWagonNumberMustBeOnlyDigits()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        //Digits + space
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['number' => '61 285970039']));
        $response->assertSessionHasErrors('number');

        //Digits + letter
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['number' => '61a285970039']));
        $response->assertSessionHasErrors('number');

        //Only letters
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['number' => 'aaaaaaaaaaaa']));
        $response->assertSessionHasErrors('number');
    }

    /**
     * Test passenger wagon number must be exactly 12 digits.
     *
     * @return void
     */
    public function testPassengerWagonNumberMustBeExactly12Digits()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        //More digits
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['number' => '6152859700391']));
        $response->assertSessionHasErrors('number');

        //Less digits
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['number' => '61528597003']));
        $response->assertSessionHasErrors('number');
    }

    /**
     * Test passenger wagon number must be unique.
     *
     * @return void
     */
    public function testPassengerWagonNumberMustBeUnique()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerWagon::factory()->create(['number' => '515219401400']);

        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['number' => '515219401400']));

        $response->assertSessionHasErrors('number');
    }

    /**
     * Test passenger wagon integer fields validation: tare, total_weight, seats_count, max_speed.
     *
     * @return void
     */
    public function testPassengerWagonIntegerFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['tare', 'total_weight', 'seats_count', 'max_speed'])
            ->each(function ($field) {
                $response = $this->post('api/passenger-wagons', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/passenger-wagons', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test passenger wagon length must be a decimal value.
     *
     * @return void
     */
    public function testPassengerWagonLengthMustBeADecimalValue()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['length' => (object)null]));
        $response->assertSessionHasErrors('length');

        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['length' => 'aaa']));
        $response->assertSessionHasErrors('length');
    }

    /**
     * Test passenger wagon string fields validation: letter_marking, brake_marking, other_info
     *
     * @return void
     */
    public function testPassengerWagonStringFieldsValidation()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        collect(['letter_marking', 'brake_marking', 'other_info'])
            ->each(function ($field) {
                $response = $this->post('api/passenger-wagons', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test passenger wagon repair_date must be a date.
     *
     * @return void
     */
    public function testPassengerWagonRepairDateMustBeADate()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['repair_date' => (object)null]));
        $response->assertSessionHasErrors('repair_date');

        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['repair_date' => 'aaa']));
        $response->assertSessionHasErrors('repair_date');

        $response = $this->post('api/passenger-wagons', array_merge($this->data, ['repair_date' => '123']));
        $response->assertSessionHasErrors('repair_date');
    }
}
