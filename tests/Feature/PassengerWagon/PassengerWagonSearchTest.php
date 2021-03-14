<?php

namespace Tests\Feature\PassengerWagon;

use App\Models\PassengerWagon;
use App\Models\PassengerWagonType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Permissions\PassengerWagonPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PassengerWagonSearchTest extends TestCase
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
        PassengerWagonType::factory()->create(['name'=>'19-40']);
        PassengerWagonType::factory()->create(['name'=>'22-97']);
        PassengerWagon::factory()->create(['number' => '505219401412']);
        PassengerWagon::factory()->create(['number' => '505219401400']);
        PassengerWagon::factory()->create(['number' => '515222970020']);
        $this->data = [
            'search_term' => '19-40 140-0',
        ];
    }
    /**
     * Test user must be logged in in order to search passenger wagons.
     *
     * @return void
     */
    public function testPassengerWagonCannotBeSearchedWithoutAuthentication()
    {
        $response = $this->post('api/passenger-wagons-search', $this->data);

        $response->assertRedirect('api/login');
    }

    /**
     * Test user must have the 'passenger-wagon-viewAny' permission in order to search passenger wagons.
     *
     * @return void
     */
    public function testPassengerWagonsCannotBeSearchedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/passenger-wagons-search', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-wagon-viewAny' permission can search passenger wagons.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeSearchedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/passenger-wagons-search', $this->data);
        $this->assertEquals('505219401400', $response['data'][0]['data']['number']);
    }

    /**
     * Test 'search_term' field is required.
     *
     * @return void
     */
    public function testPassengerWagonSearchTermIsRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/passenger-wagons-search', array_merge($this->data, ['search_term' => null]));
        $response->assertSessionHasErrors('search_term');
    }

    /**
     * Test 'search_term' field must be a string.
     *
     * @return void
     */
    public function testPassengerWagonSearchTermMustBeAString()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);
        $response = $this->post('api/passenger-wagons-search', array_merge($this->data, ['search_term' => (object)null]));
        $response->assertSessionHasErrors('search_term');
    }
}
