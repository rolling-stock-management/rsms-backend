<?php

namespace Tests\Feature\PassengerReport;

use App\Models\PassengerReport;
use App\Models\PassengerWagon;
use App\Models\Role;
use App\Models\Train;
use App\Models\User;
use Database\Seeders\Permissions\PassengerReportPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PassengerReportTest extends TestCase
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
        $this->seed(PassengerReportPermissionsSeeder::class);
        PassengerWagon::factory()->create();
        Train::factory()->create();
        $this->data = [
            'email' => 'test@email.com',
            'date' => '2021-05-06',
            'problem_description' => 'Some text to serve as a problem description...',
            'wagon_number' => 2,
            'train_id'=> 1,
            'wagon_id'=> 1,
        ];
    }


    /**
     * Test passenger report can be created.
     *
     * @return void
     */
    public function testPassengerReportCanBeCreated()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('api/passenger-reports', $this->data);
        $passengerReport = PassengerReport::first();

        $this->assertCount(1, PassengerReport::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $passengerReport->id,
                'email' => $passengerReport->email,
                'date' => $passengerReport->date->format('Y-m-d'),
                'problem_description' => $passengerReport->problem_description,
                'wagon_number' => $passengerReport->wagon_number,
                'created_at' => $passengerReport->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerReport->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-report-viewAny' permission in order to see a list of passenger report.
     *
     * @return void
     */
    public function testPassengerReportsCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/passenger-reports');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-report-viewAny' permission can see a list of passenger report.
     *
     * @return void
     */
    public function testPassengerReportesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        PassengerReport::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/passenger-reports');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test user must have the 'passenger-report-view' permission in order to view a passenger report.
     *
     * @return void
     */
    public function testPassengerReportCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerReport = PassengerReport::factory()->create();
        $response = $this->get('api/passenger-reports/' . $passengerReport->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'passenger-report-view' permission can view a passenger report.
     *
     * @return void
     */
    public function testPassengerReportCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerReport = PassengerReport::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/passenger-reports/' . $passengerReport->id);

        $response->assertJson([
            'data' => [
                'id' => $passengerReport->id,
                'email' => $passengerReport->email,
                'date' => $passengerReport->date->format('Y-m-d'),
                'problem_description' => $passengerReport->problem_description,
                'wagon_number' => $passengerReport->wagon_number,
                'created_at' => $passengerReport->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerReport->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-report-update' permission in order to update a passenger report.
     *
     * @return void
     */
    public function testPassengerReportCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerReport = PassengerReport::factory()->create();
        $response = $this->patch('api/passenger-reports/' . $passengerReport->id, $this->data);
        $passengerReport = PassengerReport::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['problem_description'], $passengerReport->problem_description);
    }

    /**
     * Test user with 'passenger-report-update' permission can update a passenger report.
     *
     * @return void
     */
    public function testPassengerReportCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $passengerReport = PassengerReport::factory()->create();
        $response = $this->patch('api/passenger-reports/' . $passengerReport->id, $this->data);
        $passengerReport = PassengerReport::first();

        $this->assertEquals($this->data['problem_description'], $passengerReport->problem_description);
        $this->assertEquals($this->data['wagon_number'], $passengerReport->wagon_number);
        $this->assertEquals($this->data['date'], $passengerReport->date->format('Y-m-d'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $passengerReport->id,
                'email' => $passengerReport->email,
                'date' => $passengerReport->date->format('Y-m-d'),
                'problem_description' => $passengerReport->problem_description,
                'wagon_number' => $passengerReport->wagon_number,
                'created_at' => $passengerReport->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $passengerReport->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'passenger-report-delete' permission in order to delete a passenger report.
     *
     * @return void
     */
    public function testPassengerReportCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $passengerReport = PassengerReport::factory()->create();
        $response = $this->delete('api/passenger-reports/' . $passengerReport->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, PassengerReport::all());
    }

    /**
     * Test user with 'passenger-report-delete' permission can delete a passenger report.
     *
     * @return void
     */
    public function testPassengerReportCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $passengerReport = PassengerReport::factory()->create();
        $response = $this->delete('api/passenger-reports/' . $passengerReport->id);

        $this->assertCount(0, PassengerReport::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
