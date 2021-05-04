<?php

namespace Tests\Feature;

use App\Models\Timetable;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Permissions\TimetablePermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TimetableTest extends TestCase
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
        $this->seed(TimetablePermissionsSeeder::class);
        $this->data = [
            'start_date' => '15-12-2020',
            'end_date' => '14-12-2021'
        ];
    }

    /**
     * Test user must be logged in in order to create an timetable.
     *
     * @return void
     */
    public function testTimetableCannotBeCreatedWithoutAuthentication()
    {
        $response = $this->post('api/timetables', $this->data);

        $response->assertRedirect('api/login');
        $this->assertCount(0, Timetable::all());
    }

    /**
     * Test user must have the 'timetable-create' permission in order to create an timetable.
     *
     * @return void
     */
    public function testTimetableCannotBeCreatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->post('api/timetables', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Timetable::all());
    }

    /**
     * Test user with 'timetable-create' permission can create an timetable.
     *
     * @return void
     */
    public function testTimetableCanBeCreatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/timetables', $this->data);
        $timetable = Timetable::first();

        $this->assertCount(1, Timetable::all());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'id' => $timetable->id,
                'start_date' => $timetable->start_date->format('Y-m-d'),
                'end_date' => $timetable->end_date->format('Y-m-d'),
                'created_at' => $timetable->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $timetable->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test timetable start and end dates are required.
     *
     * @return void
     */
    public function testTimetableStartAndEndDatesAreRequired()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['start_date', 'end_date'])
            ->each(function ($field) {
                $response = $this->post('api/timetables', array_merge($this->data, [$field => null]));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test timetable start and end dates must be dates.
     *
     * @return void
     */
    public function testTimetableStartAndEndDatesMustBeDates()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        collect(['start_date', 'end_date'])
            ->each(function ($field) {
                $response = $this->post('api/timetables', array_merge($this->data, [$field => '123']));
                $response->assertSessionHasErrors($field);

                $response = $this->post('api/timetables', array_merge($this->data, [$field => 'aaa']));
                $response->assertSessionHasErrors($field);
            });
    }

    /**
     * Test timetable dates are instances of Carbon.
     *
     * @return void
     */
    public function testTimetableDatesAreInstancesOfCarbon()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $this->post('api/timetables', $this->data);
        $timetable = Timetable::first();

        $this->assertCount(1, Timetable::all());
        $this->assertInstanceOf(Carbon::class, $timetable->start_date);
        $this->assertInstanceOf(Carbon::class, $timetable->end_date);
    }

    /**
     * Test timetable end date must be after start date.
     *
     * @return void
     */
    public function testTimetableEndDateMustBeAfterStartDate()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);

        $response = $this->post('api/timetables', array_merge($this->data, ['end_date' => '15-11-2020']));
        $response->assertSessionHasErrors('end_date');
    }

    /**
     * Test user must have the 'timetable-viewAny' permission in order to see a list of timetables.
     *
     * @return void
     */
    public function testTimetablesCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $response = $this->get('api/timetables');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'timetable-viewAny' permission can see a list of timetables.
     *
     * @return void
     */
    public function testTimetablesCanBeRetrievedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Timetable::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/timetables');

        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test timetable no-pagination option.
     *
     * @return void
     */
    public function testTimetablesPaginationCanBeTurnedOff()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        Timetable::factory()->count(11)->create();
        $this->user->roles[0]->permissions()->sync([1]);
        $response = $this->get('api/timetables?no-pagination=1');

        $response->assertJsonCount(11, 'data');
    }

    /**
     * Test user must have the 'timetable-view' permission in order to view an timetable.
     *
     * @return void
     */
    public function testTimetableCannotBeRetrievedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $timetable = Timetable::factory()->create();
        $response = $this->get('api/timetables/' . $timetable->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test user with 'timetable-view' permission can view an timetable.
     *
     * @return void
     */
    public function testTimetableCanBeRetrievedWithTheRightPermission()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $timetable = Timetable::factory()->create();
        $this->user->roles[0]->permissions()->sync([2]);
        $response = $this->get('api/timetables/' . $timetable->id);

        $response->assertJson([
            'data' => [
                'id' => $timetable->id,
                'start_date' => $timetable->start_date->format('Y-m-d'),
                'end_date' => $timetable->end_date->format('Y-m-d'),
                'created_at' => $timetable->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $timetable->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'timetable-update' permission in order to update an timetable.
     *
     * @return void
     */
    public function testTimetableCannotBeUpdatedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $timetable = Timetable::factory()->create();
        $response = $this->patch('api/timetables/' . $timetable->id, $this->data);
        $timetable = Timetable::first();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertNotEquals($this->data['start_date'], $timetable->start_date);
    }

    /**
     * Test user with 'timetable-update' permission can update an timetable.
     *
     * @return void
     */
    public function testTimetableCanBeUpdatedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $timetable = Timetable::factory()->create();
        $response = $this->patch('api/timetables/' . $timetable->id, $this->data);
        $timetable = Timetable::first();

        $this->assertEquals($this->data['start_date'], $timetable->start_date->format('d-m-Y'));
        $this->assertEquals($this->data['end_date'], $timetable->end_date->format('d-m-Y'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $timetable->id,
                'start_date' => $timetable->start_date->format('Y-m-d'),
                'end_date' => $timetable->end_date->format('Y-m-d'),
                'created_at' => $timetable->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $timetable->updated_at->format('d.m.Y h:i:s')
            ]
        ]);
    }

    /**
     * Test user must have the 'timetable-delete' permission in order to delete an timetable.
     *
     * @return void
     */
    public function testTimetableCannotBeDeletedWithoutTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $timetable = Timetable::factory()->create();
        $response = $this->delete('api/timetables/' . $timetable->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Timetable::all());
    }

    /**
     * Test user with 'timetable-delete' permission can delete an timetable.
     *
     * @return void
     */
    public function testTimetableCanBeDeletedWithTheRightPermission()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([5]);
        $timetable = Timetable::factory()->create();
        $response = $this->delete('api/timetables/' . $timetable->id);

        $this->assertCount(0, Timetable::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
