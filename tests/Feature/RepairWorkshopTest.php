<?php

namespace Tests\Feature;

use App\Models\RepairWorkshop;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RepairWorkshopPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RepairWorkshopTest extends TestCase
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
        $this->seed(RepairWorkshopPermissionsSeeder::class);
        $this->data = [
            'name' => 'repairWorkshop1',
            'abbreviation' => 'RPWS1',
            'note' => 'Some text to serve as a note to the repair workshop...',
        ];
    }

    /**
     * Test user must be logged in in order to create a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/repair-workshops', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, RepairWorkshop::all());
    }

    /**
     * Test user must have the 'repair-workshop-create' permission in order to create a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/repair-workshops', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, RepairWorkshop::all());
    }

    /**
     * Test user with 'repair-workshop-create' permission can create a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repair-workshops', $this->data);
        $repairWorkshop = RepairWorkshop::first();

        $this->assertCount(1, RepairWorkshop::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $repairWorkshop->id,
                'name' => $repairWorkshop->name,
                'abbreviation' => $repairWorkshop->abbreviation,
                'note' => $repairWorkshop->note,
                'created_at' => $repairWorkshop->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repairWorkshop->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test repair workshop name is required.
     *
     * @return void
     */
    public function testRepairWorkshopNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/repair-workshops', array_merge($this->data, ['name' => null]));
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test repair workshop name and note must be strings.
     *
     * @return void
     */
    public function testRepairWorkshopNameAndDescriptionMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'note'])
            ->each(function ($field) {
                $response = $this->post('api/repair-workshops', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'repair-workshop-viewAny' permission in order to see a list of repair workshops.
     *
     * @return void
     */
    public function testRepairWorkshopsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/repair-workshops');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'repair-workshop-viewAny' permission can see a list of repair workshops.
     *
     * @return void
     */
    public function testRepairWorkshopsCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        RepairWorkshop::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/repair-workshops');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test repair workshop no-pagination option.
     *
     * @return void
     */
    public function testRepairWorkshopsPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        RepairWorkshop::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/repair-workshops?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'repair-workshop-view' permission in order to view a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairWorkshop = RepairWorkshop::factory()->create();
        $response = $this->get('api/repair-workshops/' . $repairWorkshop->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'repair-workshop-view' permission can view a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairWorkshop = RepairWorkshop::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/repair-workshops/' . $repairWorkshop->id);

        $response->assertJson([
            'data' => [
                'id' => $repairWorkshop->id,
                'name' => $repairWorkshop->name,
                'abbreviation' => $repairWorkshop->abbreviation,
                'note' => $repairWorkshop->note,
                'created_at' => $repairWorkshop->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repairWorkshop->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'repair-workshop-update' permission in order to update a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairWorkshop = RepairWorkshop::factory()->create();
        $response = $this->patch('api/repair-workshops/' . $repairWorkshop->id, $this->data);
        $repairWorkshop = RepairWorkshop::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $repairWorkshop->name);
    }

    /**
     * Test user with 'repair-workshop-update' permission can update a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repairWorkshop = RepairWorkshop::factory()->create();
        $response = $this->patch('api/repair-workshops/' . $repairWorkshop->id, $this->data);
        $repairWorkshop = RepairWorkshop::first();

        $this->assertEquals($this->data['name'], $repairWorkshop->name);
        $this->assertEquals($this->data['abbreviation'], $repairWorkshop->abbreviation);
        $this->assertEquals($this->data['note'], $repairWorkshop->note);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $repairWorkshop->id,
                'name' => $repairWorkshop->name,
                'abbreviation' => $repairWorkshop->abbreviation,
                'note' => $repairWorkshop->note,
                'created_at' => $repairWorkshop->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repairWorkshop->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'repair-workshop-delete' permission in order to delete a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairWorkshop = RepairWorkshop::factory()->create();
        $response = $this->delete('api/repair-workshops/' . $repairWorkshop->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, RepairWorkshop::all());
    }

    /**
     * Test user with 'repair-workshop-delete' permission can delete a repair workshop.
     *
     * @return void
     */
    public function testRepairWorkshopCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $repairWorkshop = RepairWorkshop::factory()->create();
        $response = $this->delete('api/repair-workshops/' . $repairWorkshop->id);

        $this->assertCount(0, RepairWorkshop::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
