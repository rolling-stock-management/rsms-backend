<?php

namespace Tests\Feature;

use App\Models\RepairType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\RepairTypePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RepairTypeTest extends TestCase
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
        $this->seed(RepairTypePermissionsSeeder::class);
        $this->data = [
            'name' => 'repairType1',
            'description' => 'Some text to serve as a description to the repair type...',
        ];
    }

    /**
     * Test user must be logged in in order to create a repair type.
     *
     * @return void
     */
    public function testRepairTypeCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/repair-types', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, RepairType::all());
    }

    /**
     * Test user must have the 'repair-type-create' permission in order to create a repair type.
     *
     * @return void
     */
    public function testRepairTypeCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/repair-types', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, RepairType::all());
    }

    /**
     * Test user with 'repair-type-create' permission can create a repair type.
     *
     * @return void
     */
    public function testRepairTypeCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/repair-types', $this->data);
        $repairType = RepairType::first();

        $this->assertCount(1, RepairType::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $repairType->id,
                'name' => $repairType->name,
                'description' => $repairType->description,
                'created_at' => $repairType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repairType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test repair type name is required.
     *
     * @return void
     */
    public function testRepairTypeNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/repair-types', array_merge($this->data, ['name' => null]));
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test repair type name and description must be strings.
     *
     * @return void
     */
    public function testRepairTypeNameAndDescriptionMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'description'])
            ->each(function ($field) {
                $response = $this->post('api/repair-types', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'repair-type-viewAny' permission in order to see a list of repair types.
     *
     * @return void
     */
    public function testRepairTypesCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/repair-types');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'repair-type-viewAny' permission can see a list of repair types.
     *
     * @return void
     */
    public function testRepairTypesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        RepairType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/repair-types');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test repair type no-pagination option.
     *
     * @return void
     */
    public function testRepairTypesPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        RepairType::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/repair-types?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'repair-type-view' permission in order to view a repair type.
     *
     * @return void
     */
    public function testRepairTypeCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairType = RepairType::factory()->create();
        $response = $this->get('api/repair-types/' . $repairType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'repair-type-view' permission can view a repair type.
     *
     * @return void
     */
    public function testRepairTypeCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairType = RepairType::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/repair-types/' . $repairType->id);

        $response->assertJson([
            'data' => [
                'id' => $repairType->id,
                'name' => $repairType->name,
                'description' => $repairType->description,
                'created_at' => $repairType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repairType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'repair-type-update' permission in order to update a repair type.
     *
     * @return void
     */
    public function testRepairTypeCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairType = RepairType::factory()->create();
        $response = $this->patch('api/repair-types/' . $repairType->id, $this->data);
        $repairType = RepairType::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $repairType->name);
    }

    /**
     * Test user with 'repair-type-update' permission can update a repair type.
     *
     * @return void
     */
    public function testRepairTypeCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $repairType = RepairType::factory()->create();
        $response = $this->patch('api/repair-types/' . $repairType->id, $this->data);
        $repairType = RepairType::first();

        $this->assertEquals($this->data['name'], $repairType->name);
        $this->assertEquals($this->data['description'], $repairType->description);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $repairType->id,
                'name' => $repairType->name,
                'description' => $repairType->description,
                'created_at' => $repairType->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $repairType->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'repair-type-delete' permission in order to delete a repair type.
     *
     * @return void
     */
    public function testRepairTypeCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $repairType = RepairType::factory()->create();
        $response = $this->delete('api/repair-types/' . $repairType->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, RepairType::all());
    }

    /**
     * Test user with 'repair-type-delete' permission can delete a repair type.
     *
     * @return void
     */
    public function testRepairTypeCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $repairType = RepairType::factory()->create();
        $response = $this->delete('api/repair-types/' . $repairType->id);

        $this->assertCount(0, RepairType::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
