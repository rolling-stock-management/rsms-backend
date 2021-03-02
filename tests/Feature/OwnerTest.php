<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\OwnerPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OwnerTest extends TestCase
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
        $this->seed(OwnerPermissionsSeeder::class);
        $this->data = [
            'name' => 'owner1',
            'note' => 'Some text to serve as a note to the owner...',
        ];
    }

    /**
     * Test user must be logged in in order to create an owner.
     *
     * @return void
     */
    public function testOwnerCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/owners', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, Owner::all());
    }

    /**
     * Test user must have the 'owner-create' permission in order to create an owner.
     *
     * @return void
     */
    public function testOwnerCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/owners', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Owner::all());
    }

    /**
     * Test user with 'owner-create' permission can create an owner.
     *
     * @return void
     */
    public function testOwnerCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/owners', $this->data);
        $owner = Owner::first();

        $this->assertCount(1, Owner::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'note' => $owner->note,
                'created_at' => $owner->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $owner->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test owner name is required.
     *
     * @return void
     */
    public function testOwnerNameIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/owners', array_merge($this->data, ['name' => null]));
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test owner name and note must be strings.
     *
     * @return void
     */
    public function testOwnerNameAndNoteMustBeStrings()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['name', 'note'])
            ->each(function ($field) {
                $response = $this->post('api/owners', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test user must have the 'owner-viewAny' permission in order to see a list of owners.
     *
     * @return void
     */
    public function testOwnersCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/owners');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'owner-viewAny' permission can see a list of owners.
     *
     * @return void
     */
    public function testOwnersCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Owner::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/owners');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test owner no-pagination option.
     *
     * @return void
     */
    public function testOwnersPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Owner::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/owners?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'owner-view' permission in order to view an owner.
     *
     * @return void
     */
    public function testOwnerCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $owner = Owner::factory()->create();
        $response = $this->get('api/owners/' . $owner->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'owner-view' permission can view an owner.
     *
     * @return void
     */
    public function testOwnerCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $owner = Owner::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/owners/' . $owner->id);

        $response->assertJson([
            'data' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'note' => $owner->note,
                'created_at' => $owner->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $owner->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'owner-update' permission in order to update an owner.
     *
     * @return void
     */
    public function testOwnerCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $owner = Owner::factory()->create();
        $response = $this->patch('api/owners/' . $owner->id, $this->data);
        $owner = Owner::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['name'], $owner->name);
    }

    /**
     * Test user with 'owner-update' permission can update an owner.
     *
     * @return void
     */
    public function testOwnerCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $owner = Owner::factory()->create();
        $response = $this->patch('api/owners/' . $owner->id, $this->data);
        $owner = Owner::first();

        $this->assertEquals($this->data['name'], $owner->name);
        $this->assertEquals($this->data['note'], $owner->note);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'note' => $owner->note,
                'created_at' => $owner->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $owner->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'owner-delete' permission in order to delete an owner.
     *
     * @return void
     */
    public function testOwnerCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $owner = Owner::factory()->create();
        $response = $this->delete('api/owners/' . $owner->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Owner::all());
    }

    /**
     * Test user with 'owner-delete' permission can delete an owner.
     *
     * @return void
     */
    public function testOwnerCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $owner = Owner::factory()->create();
        $response = $this->delete('api/owners/' . $owner->id);

        $this->assertCount(0, Owner::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
