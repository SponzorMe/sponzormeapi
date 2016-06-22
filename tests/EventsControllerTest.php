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
			   'image' => $event->image,			   
			   'language' => $event->language,
			   //flags
			   'is_private' => $event->is_private,
			   'is_outstanding' => $event->is_outstanding,
			   //Location attributes
			   'country'=> $event->country,
			   'place_name' => $event->place_name,
			   'place_id' => $event->place_id,
			   'latitude' => $event->latitude,
			   'longitude' => $event->longitude,
			   'address' => $event->address,
			   //Foreigns
			   'organizer' => $event->user->toArray(),
			   'type' => $event->type->toArray(),
			   'tags' => $event->tags->toArray(),
			   //Dates
			   'start' => Carbon::parse($event->start)->toIso8601String(),
			   'end' => Carbon::parse($event->end)->toIso8601String(),
			   'duration' => Carbon::parse($event->start)->diffInHours(Carbon::parse($event->end)),
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
		$this->assertEquals($event->image, $data['image']);
		$this->assertEquals($event->language, $data['language']);
		//flags
		$this->assertEquals($event->is_private, $data['is_private']);
		$this->assertEquals($event->is_outstanding, $data['is_outstanding']);
		//Location attributes
		$this->assertEquals($event->country, $data['country']);
		$this->assertEquals($event->place_name, $data['place_name']);
		$this->assertEquals($event->place_id, $data['place_id']);
		$this->assertEquals($event->latitude, $data['latitude']);
		$this->assertEquals($event->longitude, $data['longitude']);
		$this->assertEquals($event->address, $data['address']);
		//Foreigns
        $this->assertEquals($event->user->toArray(), $data['organizer']);
		$this->assertEquals($event->type->toArray(), $data['type']);
		$this->assertEquals($event->tags->toArray(), $data['tags']);
		//Dates
		$this->assertEquals(Carbon::parse($event->start)->toIso8601String(), $data['start']);
		$this->assertEquals(Carbon::parse($event->end)->toIso8601String(), $data['end']);
		$this->assertEquals(Carbon::parse($event->start)->diffInHours(Carbon::parse($event->end)), $data['duration']);
		$this->assertEquals($event->created_at->diffForHumans(), $data['released']);
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
		$type = factory(\App\Type::class)->create();
		
		$this->post('/events', [
		            'title' => 'Jonh Smith',
		            'summary' => 'jonh@smith.com',
		            'description' => 'aaa aaa',
					'user_id' => $user->id,
					'type_id' => $type->id
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
		$type = factory(\App\Type::class)->create();
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
					'user_id' => $user->id,
					'type_id' => $type->id
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/events/[\d]+$#');
	}
	
	
	/** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$type = factory(\App\Type::class)->create();
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
					'user_id' => $user->id,
					'type_id' => $type->id
		        ]);
        $this->notSeeInDatabase('events', [
                    'title' => 'Jonh Papa 2',
                    'summary' => 'john2@papa.com',
                    'description' => 'aaa aaa',
					'user_id' => $user->id,
					'type_id' => $type->id
                ]);

		
		$this->put("/events/{$event->id}", [
		            'id' => 5,
		            'title' => 'Jonh Papa 2',
		            'summary' => 'john2@papa.com',
		            'description' => 'aaa aaa',
					'user_id' => $user->id,
					'type_id' => $type->id
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'id' => 1,
		            'title' => 'Jonh Papa 2',
		            'summary' => 'john2@papa.com',
		            'description' => 'aaa aaa',
					'organizer' => $user->toArray(),
					'type' => $type->toArray()
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
