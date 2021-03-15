<?php

namespace Tests\Feature\TractiveUnit;

use App\Models\TractiveUnit;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\TractiveUnitPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TractiveUnitSearchTest extends TestCase
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
        TractiveUnit::factory()->create(['number' => '915200435393']);
        TractiveUnit::factory()->create(['number' => '915200440989']);
        TractiveUnit::factory()->create(['number' => '915200440625']);
        $this->data = [
            'search_term' => '0044 062-5',
        ];
    }

    /**
     * Test user must be logged in in order to search tractive units.
     *
     * @return void
     */
    public function testTractiveUnitCannotBeSearchedWithoutAuthentication()
    {
        $response = $this->post('api/tractive-units-search', $this->data);

        $response->assertRedirect('api/login');
    }

    /**
     * Test user must have the 'tractive-unit-viewAny' permission in order to search tractive units.
     *
     * @return void
     */
    public function testTractiveUnitsCannotBeSearchedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/tractive-units-search', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'tractive-unit-viewAny' permission can search tractive units.
     *
     * @return void
     */
    public function testTractiveUnitsCanBeSearchedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/tractive-units-search', $this->data);
        $this->assertEquals('915200440625', $response['data'][0]['data']['number']);
    }

    /**
     * Test 'search_term' field is required.
     *
     * @return void
     */
    public function testTractiveUnitSearchTermIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/tractive-units-search', array_merge($this->data, ['search_term' => null]));
        $response->assertSessionHasErrors('search_term');
    }

    /**
     * Test 'search_term' field must be a string.
     *
     * @return void
     */
    public function testTractiveUnitSearchTermMustBeAString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/tractive-units-search', array_merge($this->data, ['search_term' => (object)null]));
        $response->assertSessionHasErrors('search_term');
    }
}
