<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class EventsControllerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown() {
        parent::tearDown(); 
        Carbon::setTestNow();
    }
	
	/** @test **/
	public function testShouldReturnCollectionOfEvents()
	{
		$events = $this->eventFactory(2);
		$this->get('/events');
		
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        foreach ($events as $event) {
           $this->seeJson([
               'id' => $event->id,
               'title' => $event->title,
               'summary' => $event->summary,
               'description' => $event->description,
			   'organizer' => $event->user->toArray(),
               'released' => $event->created_at->diffForHumans(),
               'created_at' => $event->created_at->toIso8601String(),
               'updated_at' => $event->updated_at->toIso8601String()
           ]);
        }
	}
	
	
	/** @test **/
	public function testShouldReturnValidEvent()
	{
		$event = $this->eventFactory();
		
		$this->get("/events/{$event->id}")->seeStatusCode(200);

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];
        
        $this->assertEquals($event->id, $data['id']);
        $this->assertEquals($event->title, $data['title']);
        $this->assertEquals($event->summary, $data['summary']);
        $this->assertEquals($event->description, $data['description']);
        $this->assertEquals($event->user->toArray(), $data['organizer']);
        $this->assertEquals($event->created_at->toIso8601String(), $data['created_at']);
        $this->assertEquals($event->updated_at->toIso8601String(), $data['updated_at']);
	}
	
	
	/** @test **/
	public function testShouldFailWhenEventIdDoesNotExist()
	{
		$this->get('/events/999999', ['Accept' => 'application/json'])
		        ->seeStatusCode(404)
		        ->seeJson([
		            'message' => 'Not Found',
		            'status'  => 404
		        ]);
	}
	
	
	/** @test **/
	public function testRouteShouldNotMatchAnInvalidRoute()
	{
		$this->get('/events/this-is-invalid');
		
		$this->assertNotRegExp(
		            '/Event not found/',
		            $this->response->getCOntent(),
		            'EventController@show route matching when it should not.'
		        );
	}
	
	
	/** @test **/
	public function testShouldSaveNewEventInDatabase()
	{
		$user = factory(\App\User::class)->create(
			[
				'name' => 'Jonh Smith',
				'email' => 'jonh@smith.com',
				'type' => 0,
			]
		);
		$this->post('/events', [
		            'title' => 'Jonh Smith',
		            'summary' => 'jonh@smith.com',
		            'description' => 'aaa aaa',
					'user_id' => $user->id
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		
		$data = $body['data'];
		
		$this->assertEquals('Jonh Smith', $data['title']);
		$this->assertEquals('jonh@smith.com', $data['summary']);
		$this->assertEquals('aaa aaa', $data['description']);
		$this->assertEquals($user->toArray(), $data['organizer']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('events', ['title' => 'Jonh Smith']);
	}
	
	
	/** @test **/
	public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
	{
		$user = factory(\App\User::class)->create(
			[
				'name' => 'Jonh Smith',
				'email' => 'jonh@smith.com',
				'type' => 0,
			]
		);
		$this->post('/events', [
		            'title' => 'Jonh Smith',
		            'summary' => 'jonh@smith.com',
		            'description' => 'aaa aaa',
					'user_id' => $user->id
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/events/[\d]+$#');
	}
	
	
	/** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$user = factory(\App\User::class)->create(
			[
				'name' => 'Jonh Smith',
				'email' => 'jonh@smith.com',
				'type' => 0,
			]
		);

		$event = factory('App\Event')->create([
		            'title' => 'Jonh Papa',
		            'summary' => 'john@papa.com',
		            'description' => 'aaa aaa',
					'user_id' => $user->id
		        ]);
        $this->notSeeInDatabase('events', [
                    'title' => 'Jonh Papa 2',
                    'summary' => 'john2@papa.com',
                    'description' => 'aaa aaa',
					'user_id' => $user->id
                ]);

		
		$this->put("/events/{$event->id}", [
		            'id' => 5,
		            'title' => 'Jonh Papa 2',
		            'summary' => 'john2@papa.com',
		            'description' => 'aaa aaa',
					'user_id' => $user->id
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'id' => 1,
		            'title' => 'Jonh Papa 2',
		            'summary' => 'john2@papa.com',
		            'description' => 'aaa aaa',
					'organizer' => $user->toArray()
		        ])->seeInDatabase('events', ['title' => 'Jonh Papa 2']);

        $body = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $body); 

        $data = $body['data'];

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
	}
	
	
	/** @test **/
	public function testUpdateShouldFailWithInvalidId()
	{
		$this
		        ->put('/events/999999999999999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Event not found'
		            ] 
		        ]);
		
	}
	
	
	/** @test **/
	public function testUpdateShouldNotMatchInvalidRoute()
	{
		$this->put('/events/this-is-invalid')->seeStatusCode(404);
	}
	
	
	/** @test **/
	public function testDestroyShouldRemoveValidEvent()
	{
		$event = $this->eventFactory();
		$this
		        ->delete("/events/{$event->id}")
		        ->seeStatusCode(204)
		        ->isEmpty();
		$this->notSeeInDatabase('events', ['id' => $event->id]);
	}
	
	/** @test **/
	public function testDestroyShouldReturn404WithInvalidId()
	{
		$this
		        ->delete('/events/99999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Event not found'
		        ] ]);
	}
	
	/** @test **/
	public function testDestroyShouldNotMatchInvalidRoute()
	{
		$this->delete('/events/this-is-invalid')->seeStatusCode(404);
	}
	
}
