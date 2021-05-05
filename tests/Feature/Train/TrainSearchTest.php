<?php

namespace Tests\Feature\Train;

use App\Models\Timetable;
use App\Models\Train;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        Timetable::factory()->create();
        Train::factory()->create(['number' => '2601']);
        Train::factory()->create(['number' => '8601']);
        Train::factory()->create(['number' => '10114']);
        $this->data = [
            'search_term' => '8601',
        ];
    }

    /**
     * Test trains can be searched.
     *
     * @return void
     */
    public function testTrainsCanBeSearched()
    {
        $response = $this->post('api/trains-search', $this->data);
        $this->assertEquals('8601', $response['data'][0]['data']['number']);
    }

    /**
     * Test 'search_term' field is required.
     *
     * @return void
     */
    public function testTrainSearchTermIsRequired()
    {
        $response = $this->post('api/trains-search', array_merge($this->data, ['search_term' => null]));
        $response->assertSessionHasErrors('search_term');
    }

    /**
     * Test 'search_term' field must be a string.
     *
     * @return void
     */
    public function testTrainSearchTermMustBeAString()
    {
        $response = $this->post('api/trains-search', array_merge($this->data, ['search_term' => (object)null]));
        $response->assertSessionHasErrors('search_term');
    }
}
