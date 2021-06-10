<?php

namespace Tests\Feature\Image;

use App\Models\FreightWagon;
use App\Models\Image;
use App\Models\PassengerWagon;
use App\Models\Role;
use App\Models\TractiveUnit;
use App\Models\User;
use Database\Seeders\Permissions\ImagePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ImageRelationshipsTest extends TestCase
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
        $this->seed(ImagePermissionsSeeder::class);
        PassengerWagon::factory()->create();
        FreightWagon::factory()->create();
        TractiveUnit::factory()->create();

        Storage::fake('local');
        $image = Image::factory()->make();
        $file = UploadedFile::fake()->image('photo.jpg');

        $this->data = [
            'title' => $image->title,
            'description' => $image->description,
            'date' => "2020-11-21",
            'file' => $file,
            'imageables' => [
                'passenger' => [],
                'freight' => [],
                'locomotive' => [],
            ],
        ];
    }

    /**
     * Test passenger wagon can be assigned to image.
     *
     * @return void
     */
    public function testPassengerWagonCanBeAssignedToImage()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);

        $response = $this->post('api/images', array_merge($this->data, ['imageables' => [
            'passenger' => [1],
            'freight' => [],
            'locomotive' => [],
        ]]));
        $image = Image::first();

        $this->assertCount(1, Image::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, $image->passengerWagons);
        $this->assertNotEmpty($response['data']['imageables']['passenger']);
        $this->assertEmpty($response['data']['imageables']['freight']);
        $this->assertEmpty($response['data']['imageables']['locomotive']);
    }

    /**
     * Test passenger wagons can be assigned to image.
     *
     * @return void
     */
    public function testPassengerWagonsCanBeAssignedToImage()
    {
        PassengerWagon::factory()->create();

        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);

        $response = $this->post('api/images', array_merge($this->data, ['imageables' => [
            'passenger' => [1, 2],
            'freight' => [],
            'locomotive' => [],
        ]]));
        $image = Image::first();

        $this->assertCount(1, Image::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(2, $image->passengerWagons);
        $this->assertNotEmpty($response['data']['imageables']['passenger']);
        $this->assertEmpty($response['data']['imageables']['freight']);
        $this->assertEmpty($response['data']['imageables']['locomotive']);
    }

    /**
     * Test passenger wagon can be updated on image.
     *
     * @return void
     */
    public function testPassengerWagonCanBeUpdatedOnImage()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([2]);
        $image = Image::factory()
            ->hasAttached(
                PassengerWagon::factory()->count(1)
            )
            ->create();

        $response = $this->patch('api/images/' . $image->id, array_merge($this->data, ['imageables' => [
            'passenger' => [1],
            'freight' => [],
            'locomotive' => [],
        ]]));
        $image = Image::first();

        $this->assertEquals($this->data['title'], $image->title);
        $this->assertEquals(1, $image->passengerWagons()->first()->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response['data']['imageables']['passenger']);
        $this->assertEmpty($response['data']['imageables']['freight']);
        $this->assertEmpty($response['data']['imageables']['locomotive']);
    }

    /**
     * Test at least one id must be present.
     *
     * @return void
     */
    public function testAtLeastOneIdMustBePresent()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);

        $response = $this->post('api/images', $this->data);
        $response->assertSessionHasErrors('imageables');
    }

    /**
     * Test ids must be valid.
     *
     * @return void
     */
    public function testIdsMustBeValid()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);

        $response = $this->post('api/images', array_merge($this->data, ['imageables' => [
            'passenger' => [2],
            'freight' => [],
            'locomotive' => [],
        ]]));
        $response->assertSessionHasErrors('imageables');
    }

    /**
     * Test freight wagon can be assigned to image.
     *
     * @return void
     */
    public function testFreightWagonCanBeAssignedToImage()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);

        $response = $this->post('api/images', array_merge($this->data, ['imageables' => [
            'passenger' => [],
            'freight' => [1],
            'locomotive' => [],
        ]]));
        $image = Image::first();

        $this->assertCount(1, Image::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, $image->freightWagons);
        $this->assertEmpty($response['data']['imageables']['passenger']);
        $this->assertNotEmpty($response['data']['imageables']['freight']);
        $this->assertEmpty($response['data']['imageables']['locomotive']);
    }

    /**
     * Test freight wagons can be assigned to image.
     *
     * @return void
     */
    public function testFreightWagonsCanBeAssignedToImage()
    {
        FreightWagon::factory()->create();

        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(1);

        $response = $this->post('api/images', array_merge($this->data, ['imageables' => [
            'passenger' => [],
            'freight' => [1, 2],
            'locomotive' => [],
        ]]));
        $image = Image::first();

        $this->assertCount(1, Image::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(2, $image->freightWagons);
        $this->assertEmpty($response['data']['imageables']['passenger']);
        $this->assertNotEmpty($response['data']['imageables']['freight']);
        $this->assertEmpty($response['data']['imageables']['locomotive']);
    }

    /**
     * Test freight wagon can be updated on image.
     *
     * @return void
     */
    public function testFreightWagonCanBeUpdatedOnImage()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([2]);
        $image = Image::factory()
            ->hasAttached(
                FreightWagon::factory()->count(1)
            )
            ->create();

        $response = $this->patch('api/images/' . $image->id, array_merge($this->data, ['imageables' => [
            'passenger' => [],
            'freight' => [1],
            'locomotive' => [],
        ]]));
        $image = Image::first();

        $this->assertEquals($this->data['title'], $image->title);
        $this->assertEquals(1, $image->freightWagons()->first()->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEmpty($response['data']['imageables']['passenger']);
        $this->assertNotEmpty($response['data']['imageables']['freight']);
        $this->assertEmpty($response['data']['imageables']['locomotive']);
    }
}
