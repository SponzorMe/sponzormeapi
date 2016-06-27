<?php

namespace Tests\Controllers;


use TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class SponsorshipTypesControllerTest extends TestCase
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
    public function testShouldReturnAValidSponsorshipType()
    {
        $sponsorshipType = $this->sponsorshipTypeFactory();

        $this->get("/sponsorship_types/{$sponsorshipType->id}", ['Accept'=> 'application/json']);
        $this->seeStatusCode(200);
		
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];

        // Check tag propierties exist in the response
        $this->assertEquals($sponsorshipType->id, $data['id']);
        $this->assertEquals($sponsorshipType->kind, $data['kind']);
        $this->assertEquals($sponsorshipType->cost, $data['cost']);
        $this->assertEquals($sponsorshipType->total_slots, $data['total_slots']);
        $this->assertEquals($sponsorshipType->used_slots, $data['used_slots']);
        $this->assertEquals($sponsorshipType->total_slots-$sponsorshipType->used_slots, $data['available_slots']);
    }

    public function testShouldSaveNewSponsorshipTypeInDatabase()
	{
		$event = $this->eventFactory();

        $this->notSeeInDatabase('sponsorship_types', ['kind' => 'My Fake Sponsorship Type']);
		
		$this->post('/sponsorship_types', [
		            'kind' => 'My Fake Sponsorship Type',
		            'cost' => 14.23,
		            'total_slots' => 5,
					'used_slots' => 1,
					'event_id' => $event->id
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		$data = $body['data'];
		
		$this->assertEquals('My Fake Sponsorship Type', $data['kind']);
		$this->assertEquals(14.23, $data['cost']);
		$this->assertEquals(5, $data['total_slots']);
        $this->assertEquals(1, $data['used_slots']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('sponsorship_types', ['kind' => 'My Fake Sponsorship Type']);
	}

    /** @test **/
	public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
	{
		$event = $this->eventFactory();

        $this->notSeeInDatabase('sponsorship_types', ['kind' => 'My Fake Sponsorship Type']);
		
		$this->post('/sponsorship_types', [
		            'kind' => 'My Fake Sponsorship Type',
		            'cost' => 14.23,
		            'total_slots' => 5,
					'used_slots' => 1,
					'event_id' => $event->id
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/sponsorship_types/[\d]+$#');
	}

    /** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$event = $this->eventFactory();

		$sponsorshipType = factory('App\SponsorshipType')->create([
		            'kind' => 'My Fake Sponsorship Type',
		            'cost' => 11.23,
		            'total_slots' =>3,
					'used_slots' => 2,
					'event_id' => $event->id
		        ]);
        $this->notSeeInDatabase('sponsorship_types', [
                    'kind' => 'My Fake Sponsorship Type 2',
		            'cost' => 14.23,
		            'total_slots' => 5,
					'used_slots' => 1,
					'event_id' => $event->id
                ]);

		
		$this->put("/sponsorship_types/{$sponsorshipType->id}", [
		            'kind' => 'My Fake Sponsorship Type 2',
		            'cost' => 14.23,
		            'total_slots' => 5,
					'used_slots' => 1,
					'event_id' => $event->id
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'kind' => 'My Fake Sponsorship Type 2',
		            'cost' => 14.23,
		            'total_slots' => 5,
					'used_slots' => 1
		        ])->seeInDatabase('sponsorship_types', ['kind' => 'My Fake Sponsorship Type 2']);

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
		        ->put('/sponsorship_types/999999999999999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'SponsorshipType not found'
		            ] 
		        ]);
		
	}
	
	
	/** @test **/
	public function testUpdateShouldNotMatchInvalidRoute()
	{
		$this->put('/sponsorship_types/this-is-invalid')->seeStatusCode(404);
	}

    /** @test **/
	public function testDestroyShouldRemoveValidSponsorshipType()
	{
		$sponsorshipType = $this->sponsorshipTypeFactory();
		$this
		        ->delete("/sponsorship_types/{$sponsorshipType->id}")
		        ->seeStatusCode(204)
		        ->isEmpty();
		$this->notSeeInDatabase('sponsorship_types', ['id' => $sponsorshipType->id]);
	}
	
	/** @test **/
	public function testDestroyShouldReturn404WithInvalidId()
	{
		$this
		        ->delete('/sponsorship_types/99999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'SponsorshipType not found'
		        ] ]);
	}
	
	/** @test **/
	public function testDestroyShouldNotMatchInvalidRoute()
	{
		$this->delete('/sponsorship_types/this-is-invalid')->seeStatusCode(404);
	}
}
