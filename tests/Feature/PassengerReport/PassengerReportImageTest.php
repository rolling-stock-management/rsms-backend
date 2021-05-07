<?php

namespace Tests\Feature\PassengerReport;

use App\Models\PassengerReport;
use App\Models\PassengerWagon;
use App\Models\Role;
use App\Models\Train;
use App\Models\User;
use Database\Seeders\Permissions\PassengerReportPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PassengerReportImageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->user = User::factory()->create();
        Role::factory()->create();
        $this->user->roles()->sync(1);
        $this->seed(PassengerReportPermissionsSeeder::class);
        PassengerWagon::factory()->create();
        Train::factory()->create();

        $file = UploadedFile::fake()->image('photo.jpg');

        $this->data = [
            'email' => 'test@email.com',
            'date' => '2021-05-06',
            'problem_description' => 'Some text to serve as a problem description...',
            'wagon_number' => 2,
            'train_id'=> 1,
            'wagon_id'=> 1,
            'image' => $file
        ];
    }

    /**
     * Test image can be uploaded.
     *
     * @return void
     */
    public function testPassengerReportImageCanBeUploaded()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('api/passenger-reports', $this->data);
        $passengerReport = PassengerReport::first();

        $this->assertCount(1, PassengerReport::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertNotNull($passengerReport->image_file_name);
        $this->assertNotNull($response['data']['image_file_name']);
        Storage::disk('local')->assertExists("images/" . $passengerReport->image_file_name);
    }

    /**
     * Test image thumbnail is created.
     *
     * @return void
     */
    public function testPassengerReportImageThumbnailIsCreated()
    {
        $response = $this->post('api/passenger-reports', $this->data);
        $passengerReport = PassengerReport::first();

        $this->assertCount(1, PassengerReport::all());
        $response->assertStatus(Response::HTTP_CREATED);
        Storage::disk('local')->assertExists("images/thumbnails/" . $passengerReport->image_file_name);
    }

    /**
     * Test image is removed from storage when passenger report is deleted.
     *
     * @return void
     */
    public function testImageIsRemovedFromStorageWhenPassengerReportIsDeleted()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $this->post('api/passenger-reports', $this->data);
        $passengerReport = PassengerReport::first();

        $response = $this->delete('api/passenger-reports/' . $passengerReport->id);

        $this->assertCount(0, PassengerReport::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        Storage::disk('local')->assertMissing("images/" . $passengerReport->image_file_name);
        Storage::disk('local')->assertMissing("images/thumbnails/" . $passengerReport->image_file_name);
    }
}
