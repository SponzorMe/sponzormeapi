<?php

namespace Tests\Controllers;


use TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class PerksControllerTest extends TestCase
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
        $perk = $this->perkFactory();

        $this->get("/perks/{$perk->id}", ['Accept'=> 'application/json']);
        $this->seeStatusCode(200);
		
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];

        // Check tag propierties exist in the response
        $this->assertEquals($perk->id, $data['id']);
        $this->assertEquals($perk->title, $data['title']);
        $this->assertEquals($perk->description, $data['description']);
    }

    public function testShouldSaveNewSponsorshipTypeInDatabase()
	{
		$sponsorship_type = $this->sponsorshipTypeFactory();

        $this->notSeeInDatabase('perks', ['title' => 'My Fake Perk Title']);
		
		$this->post('/perks', [
		            'title' => 'My Fake Perk Title',
		            'description' => 'My Fake Perk Description',
					'sponsorship_type_id' => $sponsorship_type->id
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		$data = $body['data'];
		
		$this->assertEquals('My Fake Perk Title', $data['title']);
		$this->assertEquals('My Fake Perk Description', $data['description']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('perks', ['title' => 'My Fake Perk Title']);
	}

    /** @test **/
	public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
	{
		$sponsorship_type = $this->sponsorshipTypeFactory();

        $this->notSeeInDatabase('perks', ['title' => 'My Fake Perk Title']);
		
		$this->post('/perks', [
		            'title' => 'My Fake Perk Title',
		            'description' => 'My Fake Perk Description',
					'sponsorship_type_id' => $sponsorship_type->id
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/perks/[\d]+$#');
	}

    /** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$sponsorship_type = $this->sponsorshipTypeFactory();

		$perk = factory('App\Perk')->create([
		            'title' => 'My Fake Perk Title',
		            'description' => 'My Fake Perk Description',
					'sponsorship_type_id' => $sponsorship_type->id
		        ]);
        $this->notSeeInDatabase('perks', [
                    'title' => 'My Fake Perk Title 2',
		            'description' => 'My Fake Perk Description 2',
					'sponsorship_type_id' => $sponsorship_type->id
                ]);

		
		$this->put("/perks/{$perk->id}", [
		            'title' => 'My Fake Perk Title 2',
		            'description' => 'My Fake Perk Description 2',
					'sponsorship_type_id' => $sponsorship_type->id
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'title' => 'My Fake Perk Title 2',
		            'description' => 'My Fake Perk Description 2'
		        ])->seeInDatabase('perks', ['title' => 'My Fake Perk Title 2']);

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
		        ->put('/perks/999999999999999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Perk not found'
		            ] 
		        ]);
		
	}
	
	
	/** @test **/
	public function testUpdateShouldNotMatchInvalidRoute()
	{
		$this->put('/perks/this-is-invalid')->seeStatusCode(404);
	}

    /** @test **/
	public function testDestroyShouldRemoveValidSponsorshipType()
	{
		$perk = $this->perkFactory();
		$this
		        ->delete("/perks/{$perk->id}")
		        ->seeStatusCode(204)
		        ->isEmpty();
		$this->notSeeInDatabase('perks', ['id' => $perk->id]);
	}
	
	/** @test **/
	public function testDestroyShouldReturn404WithInvalidId()
	{
		$this
		        ->delete('/perks/99999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Perk not found'
		        ] ]);
	}
	
	/** @test **/
	public function testDestroyShouldNotMatchInvalidRoute()
	{
		$this->delete('/perks/this-is-invalid')->seeStatusCode(404);
	}
}
