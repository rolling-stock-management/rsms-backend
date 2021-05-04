<?php

namespace Tests\Feature\Train;

use App\Models\Role;
use App\Models\Timetable;
use App\Models\Train;
use App\Models\User;
use Database\Seeders\Permissions\TrainPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TrainRelationshipsTest extends TestCase
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
        $this->seed(TrainPermissionsSeeder::class);
        Timetable::factory()->create();
        $this->data = [
            'number' => '8601',
            'route' => 'Sofia - Plovdiv - Burgas',
            'note' => 'Some text to serve as a note to the train...',
            'timetable_id' => 1
        ];
    }

    /**
     * Test train timetable_id must be an integer.
     *
     * @return void
     */
    public function testTrainTimetableIdMustBeAnInteger()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/trains', array_merge($this->data, ['timetable_id' => (object)null]));
        $response->assertSessionHasErrors('timetable_id');

        $response = $this->post('api/trains', array_merge($this->data, ['timetable_id' => 'aaa']));
        $response->assertSessionHasErrors('timetable_id');
    }

    /**
     * Test train timetable_id must exist.
     *
     * @return void
     */
    public function testTrainTimetableIdMustExist()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([3]);
        $response = $this->post('api/trains', array_merge($this->data, ['timetable_id' => 5]));
        $response->assertSessionHasErrors('timetable_id');
    }

    /**
     * Test timetable can be assigned to train.
     *
     * @return void
     */
    public function testTimetableCanBeAssignedToTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync(3);
        $response = $this->post('api/trains', $this->data);
        Train::first();

        $this->assertCount(1, Train::all());
        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotNull($response['data']['timetable']);
    }

    /**
     * Test timetable can be updated on train.
     *
     * @return void
     */
    public function testTimetableCanBeUpdatedOnTrain()
    {
        Sanctum::actingAs(
            $this->user,
            ['*']
        );
        $this->user->roles[0]->permissions()->sync([4]);
        $train = Train::factory()->create();
        Timetable::factory()->create();
        $response = $this->patch('api/trains/' . $train->id, array_merge($this->data, ['timetable_id' => 2]));
        $train = Train::first();

        $this->assertEquals($this->data['number'], $train->number);
        $this->assertEquals(2, $train->timetable->id);
        $response->assertStatus(Response::HTTP_OK);
    }
}
