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
		$body = $content['data'];

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
			   //Dates
			   'timezone' => $event->timezone,
			   'start' => Carbon::parse($event->start)->toIso8601String(),
			   'end' => Carbon::parse($event->end)->toIso8601String(),
			   'duration' => Carbon::parse($event->start)->diffInHours(Carbon::parse($event->end)). ' Hours',
               'released' => $event->created_at->diffForHumans(),
               'created_at' => $event->created_at->toIso8601String(),
               'updated_at' => $event->updated_at->toIso8601String()
           ]);
		   //foreigns
		   $this->assertArrayHasKey('organizer', $body[0]);
		   $this->assertArrayHasKey('type', $body[0]);
		   $this->assertArrayHasKey('tags', $body[0]);
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
		$this->assertEquals($event->timezone, $data['timezone']);
		//Foreigns
        $this->assertEquals($event->user->id, $data['organizer']['data']['id']);
		$this->assertEquals($event->type->id, $data['type']['data']['id']);
		$this->assertEquals($event->tags->toArray(), $data['tags']['data']);
		//Dates
		$this->assertTrue(Carbon::parse($event->start)->lt(Carbon::parse($event->end)));
		$this->assertEquals(Carbon::parse($event->start)->toIso8601String(), $data['start']);
		$this->assertEquals(Carbon::parse($event->end)->toIso8601String(), $data['end']);
		$this->assertEquals(Carbon::parse($event->start)->diffInHours(Carbon::parse($event->end)).' Hours', $data['duration']);
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

		$eventValuable = factory(\App\Event::class)->make();
		
		$this->post('/events', [
		            'title' => 'My Fake Event',
		            'summary' => 'loremp Ipsump',
		            'description' => 'simplicy is the last sofistication',
					'user_id' => $user->id,
					'type_id' => $type->id,
					'start' =>  $eventValuable->start,
        			'end' => $eventValuable->end,
					'timezone' => $eventValuable->timezone
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		
		$data = $body['data'];
		
		$this->assertEquals('My Fake Event', $data['title']);
		$this->assertEquals('loremp Ipsump', $data['summary']);
		$this->assertEquals('simplicy is the last sofistication', $data['description']);
		$this->assertEquals($user->id, $data['organizer']['data']['id']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('events', ['title' => 'My Fake Event']);
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
		$eventValuable = factory(\App\Event::class)->make();
		
		$this->post('/events', [
		            'title' => 'My Fake Event',
		            'summary' => 'loremp Ipsump',
		            'description' => 'simplicy is the last sofistication',
					'user_id' => $user->id,
					'type_id' => $type->id,
					'start' =>  $eventValuable->start,
        			'end' => $eventValuable->end,
					'timezone' => $eventValuable->timezone
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/events/[\d]+$#');
	}
	
	
	/** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$type = factory(\App\Type::class)->create();
		$startDate = Carbon::now()->toIso8601String();
		$endDate = Carbon::now()->addHours(2)->toIso8601String();
    	$timezone = Carbon::now()->tzName;
		$user = factory(\App\User::class)->create(
			[
				'name' => 'Jonh Smith',
				'email' => 'jonh@smith.com',
				'type' => 0,
			]
		);

		$event = factory('App\Event')->create([
		            'title' => 'My Fake Event',
		            'summary' => 'Summary of my fake event',
		            'description' => 'aaa aaa',
					'user_id' => $user->id,
					'type_id' => $type->id,
		        	'start' =>  $startDate,
        			'end' => $endDate,
					'timezone' => $timezone
		        ]);
        $this->notSeeInDatabase('events', [
                    'title' => 'My Fake Event 2',
                    'summary' => 'Summary of my fake event 2',
                    'description' => 'aaa aaa',
					'user_id' => $user->id,
					'type_id' => $type->id,
		        	'start' =>  $startDate,
        			'end' => $endDate,
					'timezone' => $timezone
                ]);

		
		$this->put("/events/{$event->id}", [
		            'id' => 5,
		            'title' => 'My Fake Event 2',
		            'summary' => 'Summary of my fake event 2',
		            'description' => 'aaa aaa',
					'user_id' => $user->id,
					'type_id' => $type->id,
		        	'start' =>  $startDate,
        			'end' => $endDate,
					'timezone' => $timezone
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'id' => 1,
		            'title' => 'My Fake Event 2',
		            'summary' => 'Summary of my fake event 2',
		            'description' => 'aaa aaa',
		        	'start' =>  $startDate,
        			'end' => $endDate,
					'timezone' => $timezone
		        ])->seeInDatabase('events', ['title' => 'My Fake Event 2']);

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
