<?php

namespace Tests\Feature\FreightWagon;

use App\Models\FreightWagon;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\FreightWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FreightWagonSearchTest extends TestCase
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
        FreightWagon::factory()->create(['number' => '335249560020']);
        FreightWagon::factory()->create(['number' => '845266510708']);
        FreightWagon::factory()->create(['number' => '335249564061']);
        $this->data = [
            'search_term' => '4956 406-1',
        ];
    }
    /**
     * Test user must be logged in in order to search freight wagons.
     *
     * @return void
     */
    public function testFreightWagonCannotBeSearchedWithoutAuthentication()
    {
        $response = $this->post('api/freight-wagons-search', $this->data);

        $response->assertRedirect('api/login');
    }

    /**
     * Test user must have the 'freight-wagon-viewAny' permission in order to search freight wagons.
     *
     * @return void
     */
    public function testFreightWagonsCannotBeSearchedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/freight-wagons-search', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'freight-wagon-viewAny' permission can search freight wagons.
     *
     * @return void
     */
    public function testFreightWagonsCanBeSearchedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/freight-wagons-search', $this->data);
        $this->assertEquals('335249564061', $response['data'][0]['data']['number']);
    }

    /**
     * Test 'search_term' field is required.
     *
     * @return void
     */
    public function testFreightWagonSearchTermIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/freight-wagons-search', array_merge($this->data, ['search_term' => null]));
        $response->assertSessionHasErrors('search_term');
    }

    /**
     * Test 'search_term' field must be a string.
     *
     * @return void
     */
    public function testFreightWagonSearchTermMustBeAString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/freight-wagons-search', array_merge($this->data, ['search_term' => (object)null]));
        $response->assertSessionHasErrors('search_term');
    }
}
