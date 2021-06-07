<?php

namespace Tests\Feature\Image;

use App\Models\Image;
use App\Models\PassengerWagon;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Permissions\ImagePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    protected $data;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(ImagePermissionsSeeder::class);
        $this->user->roles[0]->permissions()->sync([1]);

        Storage::fake('local');
        $image = Image::factory()->make();
        $file = UploadedFile::fake()->image('photo.jpg');

        PassengerWagon::factory()->create();

        $this->data = [
            'title' => $image->title,
            'description' => $image->description,
            'date' => "2020-11-21",
            'file' => $file,
            'imageable_types' => [1],
            'imageable_ids' => [1],
        ];
    }

    /**
     * Test that a user must be logged in in order to upload an image.
     *
     * @return void
     */
    public function testImageCannotBeUploadedWithoutLogin()
    {
        $response = $this->post('api/images');

        $response->assertRedirect('api/login');
        $this->assertCount(0, Image::all());
        Storage::disk('local')->assertMissing('photo.jpg');
    }

    /**
     * Test user must have 'image-create' permission to upload images.
     *
     * @return void
     */
    public function testImageCannotBeUploadedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $this->user->roles[0]->permissions()->sync([]);
        $response = $this->post('api/images', $this->data);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Image::all());
        Storage::disk('local')->assertMissing('photo.jpg');
    }

    /**
     * Test image can be uploaded using the api.
     *
     * @return void
     */

    public function testImageCanBeUploaded()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $response = $this->post('api/images', $this->data);
        $image = Image::first();

        $this->assertCount(1, Image::all());
        $response->assertStatus(Response::HTTP_CREATED);
        Storage::disk('local')->assertExists("images/" . $image->file_name);
        $response->assertJson([
            'data' => [
                'id' => $image->id,
                'file_name' => $image->file_name,
                'title' => $image->title,
                'description' => $image->description
            ]
        ]);
    }

    /**
     * Test thumbnail is created when an image is uploaded.
     *
     * @return void
     */
    public function testThumbnailIsCreatedWhenAnImageIsUploaded()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $response = $this->post('api/images', $this->data);
        $image = Image::first();
        $response->assertStatus(Response::HTTP_CREATED);

        Storage::disk('local')->assertExists("images/thumbnails/" . $image->file_name);
    }

    /**
     * Test image title and the file itself are required when uploading.
     *
     * @return void
     */
    public function testImageTitleAndFileItselfAreRequiredValidationWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        collect(['title', 'file'])
            ->each(function ($field) {
                $response = $this->post('api/images', array_merge($this->data, [$field => null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test image title and description must be strings validation.
     *
     * @return void
     */
    public function testImageTitleAndDescriptionMustBeStringsValidationWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        collect(['title', 'description'])
            ->each(function ($field) {
                $response = $this->post('api/images', array_merge($this->data, [$field => (object)null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test image date must be valid date when uploading.
     *
     * @return void
     */
    public function testImageDateMustBeAValidDateWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $response = $this->post('api/images', array_merge($this->data, ['date' => "abcdef"]));
        $response->assertSessionHasErrors('date');

        $response = $this->post('api/images', array_merge($this->data, ['date' => "abcdef12"]));
        $response->assertSessionHasErrors('date');

        $response = $this->post('api/images', array_merge($this->data, ['date' => "21313"]));
        $response->assertSessionHasErrors('date');
    }

    /**
     * Test image date is stored properly when uploading. (Date is instance of Carbon)
     *
     * @return void
     */
    public function testImageDateIsStoredProperlyWhenCreating()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $this->post('api/images', $this->data);

        $this->assertCount(1, Image::all());
        $this->assertInstanceOf(Carbon::class, Image::first()->date);
    }

    /**
     * Test image data can be retrieved. (proper JSON is returned)
     *
     * @return void
     */
    public function testImageDataCanBeRetrieved()
    {
        $image = Image::factory()->create();

        $response = $this->get('api/images/' . $image->id);

        $image = Image::first();
        $response->assertJson([
            'data' => [
                'id' => $image->id,
                'file_name' => $image->file_name,
                'title' => $image->title,
                'description' => $image->description,
                'date' => $image->date->format('d.m.Y')
            ]
        ]);
    }

    /**
     * Test images list can be retrieved.
     *
     * @return void
     */
    public function testImagesCanBeRetrieved()
    {
        Image::factory()->create();
        Image::factory()->create();
        Image::factory()->create();

        $response = $this->get('api/images');
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test that a user must be logged in in order to update image details.
     *
     * @return void
     */
    public function testImageDetailsCannotBeUpdatedWithoutLogin()
    {
        $image = Image::factory()->create();
        $response = $this->patch('api/images/' . $image->id);
        $response->assertRedirect('api/login');
    }

    /**
     * Test image title, description and date can be updated by the owner.
     *
     * @return void
     */
    public function testImageDetailsCanBeUpdatedByOwner()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $image = Image::factory()->create(['user_id' => $this->user->id]); //Specify the owner

        $response = $this->patch('api/images/' . $image->id, $this->data);
        $result = Image::first();

        $this->assertEquals($this->data["title"], $result->title);
        $this->assertEquals($this->data["description"], $result->description);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $image->id,
                'title' => $this->data["title"],
                'description' => $this->data["description"],
                'date' => Carbon::parse($this->data["date"])->format('d.m.Y')
            ]
        ]);
    }

    /**
     * Test image details can't be updated by other user who is not owner and doesn't have the 'image-update' permission.
     *
     * @return void
     */
    public function testImageDetailsCannotBeUpdatedByOtherUserWhoIsNotOwner()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $image = Image::factory()->create(); //Creates an image with different user_id

        $response = $this->patch('api/images/' . $image->id, $this->data);
        $result = Image::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data["title"], $result->title);
    }

    /**
     * Test image details can be updated by other user with 'image-update' permission.
     *
     * @return void
     */
    public function testImageDetailsCanBeUpdatedByOtherUserWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([2]);

        $image = Image::factory()->create(); //Creates an image with different user_id

        $response = $this->patch('api/images/' . $image->id, $this->data);
        $result = Image::first();

        $this->assertEquals($this->data["title"], $result->title);
        $this->assertEquals($this->data["description"], $result->description);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $image->id,
                'title' => $this->data["title"],
                'description' => $this->data["description"],
                'date' => Carbon::parse($this->data["date"])->format('d.m.Y')
            ]
        ]);
    }

    /**
     * Test that a user must be logged in in order to delete an image. Assert redirect to /login.
     *
     * @return void
     */
    public function testImageCannotBeDeletedWithoutLogin()
    {
        $image = Image::factory()->create();
        $response = $this->delete('api/images/' . $image->id);
        $this->assertCount(1, Image::all());
        $response->assertRedirect('api/login');
    }

    /**
     * Test image can be deleted by owner.
     *
     * @return void
     */
    public function testImageCanBeDeletedByOwner()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $this->post('api/images', $this->data);
        $image = Image::first();
        $response = $this->delete('api/images/' . $image->id);

        $this->assertCount(0, Image::all());
        Storage::disk('local')->assertMissing("images/" . $image->file_name);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test image can't be deleted by other user who is not owner.
     *
     * @return void
     */
    public function testImageCannotBeDeletedByOtherUserWhoIsNotOwner()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $image = Image::factory()->create();
        $response = $this->delete('api/images/' . $image->id);

        $this->assertCount(1, Image::all());
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test image can be deleted by other user with 'image-delete' permission.
     *
     * @return void
     */
    public function testImageCanBeDeletedByOtherUserWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $this->user->roles[0]->permissions()->sync([2]);

        $image = Image::factory()->create();
        $response = $this->delete('api/images/' . $image->id);

        $this->assertCount(0, Image::all());
        Storage::disk('local')->assertMissing("images/" . $image->file_name);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
