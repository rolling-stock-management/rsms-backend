<?php

namespace Tests\Feature\Repair;

use App\Models\PassengerWagon;
use App\Models\Repair;
use App\Models\RepairType;
use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RepairPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RepairTest extends TestCase
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
        $this->seed(RepairPermissionsSeeder::class);
        RepairType::factory()->create();
        RepairWorkshop::factory()->create();
        PassengerWagon::factory()->create();
        $this->data = [
            'short_description' => 'repair1',
            'type_id' => 1,
            'workshop_id' => 1,
            'repairable_type' => 1,
            'repairable_id' => 1,
            'description' => 'Some text to serve as a description of the repair...',
            'start_date' => '2021-03-31',
            'end_date' => '2021-04-21',
        ];
    }

    /**
     * Test user must be logged in in order to create a repair.
     *
     * @return void
     */
    public function testRepairCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/repairs', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, Repair::all());
    }

    /**
     * Test user must have the 'repair-create' permission in order to create a repair.
     *
     * @return void
     */
    public function testRepairCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/repairs', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Repair::all());
    }

    /**
     * Test user with 'repair-create' permission can create a repair.
     *
     * @return void
     */
    public function testRepairCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repairs', $this->data);
        $repair = Repair::first();

        $this->assertCount(1, Repair::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $repair->id,
                'short_description' => $repair->short_description,
                'description' => $repair->description,
                'start_date' => $repair->start_date->format('Y-m-d'),
                'end_date' => $repair->end_date->format('Y-m-d'),
                'created_at' => $repair->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repair->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test repair short_description, type_id, workshop_id and start_date are required.
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
        collect(['short_description', 'type_id', 'workshop_id', 'start_date'])
            ->each(function ($field) {
                $response = $this->post('api/repairs', array_merge($this->data, [$field => null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test repair short_description and description must be strings.
     *
     * @return void
     */
    public function testRepairDescriptionsMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['short_description', 'description'])
            ->each(function ($field) {
                $response = $this->post('api/repairs', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test repair start and end dates must be dates.
     *
     * @return void
     */
    public function testRepairStartEndDatesMustBeDates()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['start_date', 'end_date'])
            ->each(function ($field) {
                $response = $this->post('api/repairs', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/repairs', array_merge($this->data, [$field => '1']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'repair-viewAny' permission in order to see a list of repairs.
     *
     * @return void
     */
    public function testRepairsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/repairs');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'repair-viewAny' permission can see a list of repairs.
     *
     * @return void
     */
    public function testRepairsCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Repair::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/repairs');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'repair-view' permission in order to view a repair.
     *
     * @return void
     */
    public function testRepairCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repair = Repair::factory()->create();
        $response = $this->get('api/repairs/' . $repair->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'repair-view' permission can view a repair.
     *
     * @return void
     */
    public function testRepairCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repair = Repair::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/repairs/' . $repair->id);

        $response->assertJson([
            'data' => [
                'id' => $repair->id,
                'short_description' => $repair->short_description,
                'description' => $repair->description,
                'start_date' => $repair->start_date->format('Y-m-d'),
                'end_date' => $repair->end_date->format('Y-m-d'),
                'created_at' => $repair->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repair->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'repair-update' permission in order to update a repair.
     *
     * @return void
     */
    public function testRepairCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repair = Repair::factory()->create();
        $response = $this->patch('api/repairs/' . $repair->id, $this->data);
        $repair = Repair::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['short_description'], $repair->short_description);
    }

    /**
     * Test user with 'repair-update' permission can update a repair.
     *
     * @return void
     */
    public function testRepairCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repair = Repair::factory()->for(
            PassengerWagon::factory(), 'repairable'
        )->create();
        $response = $this->patch('api/repairs/' . $repair->id, $this->data);
        $repair = Repair::first();

        $this->assertEquals($this->data['short_description'], $repair->short_description);
        $this->assertEquals($this->data['description'], $repair->description);
        $this->assertEquals($this->data['start_date'], $repair->start_date->format('Y-m-d'));
        $this->assertEquals($this->data['end_date'], $repair->end_date->format('Y-m-d'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $repair->id,
                'short_description' => $repair->short_description,
                'description' => $repair->description,
                'start_date' => $repair->start_date->format('Y-m-d'),
                'end_date' => $repair->end_date->format('Y-m-d'),
                'created_at' => $repair->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repair->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'repair-delete' permission in order to delete a repair.
     *
     * @return void
     */
    public function testRepairCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repair = Repair::factory()->create();
        $response = $this->delete('api/repairs/' . $repair->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Repair::all());
    }

    /**
     * Test user with 'repair-delete' permission can delete a repair.
     *
     * @return void
     */
    public function testRepairCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $repair = Repair::factory()->create();
        $response = $this->delete('api/repairs/' . $repair->id);

        $this->assertCount(0, Repair::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
