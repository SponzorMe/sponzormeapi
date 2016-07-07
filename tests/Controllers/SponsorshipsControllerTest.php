<?php

namespace Tests\Controllers;


use TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class SponsorshipsControllerTest extends TestCase
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
        $sponsorship = $this->sponsorshipFactory();

        $this->get("/sponsorships/{$sponsorship->id}", ['Accept'=> 'application/json']);
        $this->seeStatusCode(200);
		
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];

        // Check tag propierties exist in the response
        $this->assertEquals($sponsorship->id, $data['id']);
        $this->assertEquals($sponsorship->cause, $data['cause']);
        $this->assertEquals($sponsorship->sponsor_id, $data['sponsor_id']);
        $this->assertEquals($sponsorship->sponsorship_type_id, $data['sponsorship_type_id']);
    }

    public function testShouldSaveNewSponsorshipTypeInDatabase()
	{
		$sponsorship_type = $this->sponsorshipTypeFactory();
        $user = factory(\App\User::class)->create();

        $this->notSeeInDatabase('sponsorships', ['cause' => 'My Fake Sponsorship Cause']);
		
		$this->post('/sponsorships', [
		            'cause' => 'My Fake Sponsorship Cause',
					'sponsorship_type_id' => $sponsorship_type->id,
                    'sponsor_id' => $user->id
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		$data = $body['data'];
		
		$this->assertEquals('My Fake Sponsorship Cause', $data['cause']);
		$this->assertEquals($sponsorship_type->id, $data['sponsorship_type_id']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('sponsorships', ['cause' => 'My Fake Sponsorship Cause']);
	}

    /** @test **/
	public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
	{
		$sponsorship_type = $this->sponsorshipTypeFactory();
        $user = factory(\App\User::class)->create();

        $this->notSeeInDatabase('sponsorships', ['cause' => 'My Fake Sponsorship Cause']);
		
		$this->post('/sponsorships', [
		            'cause' => 'My Fake Sponsorship Cause',
					'sponsorship_type_id' => $sponsorship_type->id,
                    'sponsor_id' => $user->id
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/sponsorships/[\d]+$#');
	}

    /** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$sponsorship_type = $this->sponsorshipTypeFactory();
        $user = factory(\App\User::class)->create();

		$sponsorship = factory('App\Sponsorship')->create([
		            'cause' => 'My Fake Sponsorship Cause',
		            'sponsorship_type_id' => $sponsorship_type->id,
                    'sponsor_id' => $user->id
		        ]);
        $this->notSeeInDatabase('sponsorships', [
                    'cause' => 'My Fake Sponsorship Cause 2',
		            'sponsorship_type_id' => $sponsorship_type->id,
                    'sponsor_id' => $user->id
                ]);

		
		$this->put("/sponsorships/{$sponsorship->id}", [
		            'cause' => 'My Fake Sponsorship Cause 2',
		            'sponsorship_type_id' => $sponsorship_type->id,
                    'sponsor_id' => $user->id
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'cause' => 'My Fake Sponsorship Cause 2',
		            'sponsorship_type_id' => $sponsorship_type->id,
                    'sponsor_id' => $user->id
		        ])->seeInDatabase('sponsorships', ['cause' => 'My Fake Sponsorship Cause 2']);

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
		        ->put('/sponsorships/999999999999999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Sponsorship not found'
		            ] 
		        ]);
		
	}
	
	
	/** @test **/
	public function testUpdateShouldNotMatchInvalidRoute()
	{
		$this->put('/sponsorships/this-is-invalid')->seeStatusCode(404);
	}

    /** @test **/
	public function testDestroyShouldRemoveValidSponsorshipType()
	{
		$sponsorship = $this->sponsorshipFactory();
		$this
		        ->delete("/sponsorships/{$sponsorship->id}")
		        ->seeStatusCode(204)
		        ->isEmpty();
		$this->notSeeInDatabase('sponsorships', ['id' => $sponsorship->id]);
	}
	
	/** @test **/
	public function testDestroyShouldReturn404WithInvalidId()
	{
		$this
		        ->delete('/sponsorships/99999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Sponsorship not found'
		        ] ]);
	}
	
	/** @test **/
	public function testDestroyShouldNotMatchInvalidRoute()
	{
		$this->delete('/sponsorships/this-is-invalid')->seeStatusCode(404);
	}
}
