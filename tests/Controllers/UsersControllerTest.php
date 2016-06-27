<?php

namespace Tests\Controllers;
use TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class UsersControllerTest extends TestCase
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
	public function testShouldReturnCollectionOfUsers()
	{
		$users = factory('App\User', 2)->create();
		$this->get('/users');
		
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        foreach ($users as $user) {
           $this->seeJson([
               'id' => $user->id,
               'name' => $user->name,
               'email' => $user->email,
               'type' => $user->type,
               'released' => $user->created_at->diffForHumans(),
               'created_at' => $user->created_at->toIso8601String(),
               'updated_at' => $user->updated_at->toIso8601String()
           ]);
        }
	}
	
	
	/** @test **/
	public function testShouldReturnValidUser()
	{
		$user = factory('App\User')->create();
		
		$this->get("/users/{$user->id}")
		        ->seeStatusCode(200);

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];
        
        $this->assertEquals($user->id, $data['id']);
        $this->assertEquals($user->name, $data['name']);
        $this->assertEquals($user->email, $data['email']);
        $this->assertEquals($user->type, $data['type']);
        $this->assertEquals($user->created_at->toIso8601String(), $data['created_at']);
        $this->assertEquals($user->updated_at->toIso8601String(), $data['updated_at']);
	}
	
	
	/** @test **/
	public function testShouldFailWhenUserIdDoesNotExist()
	{
		$this->get('/users/999999', ['Accept' => 'application/json'])
		        ->seeStatusCode(404)
		        ->seeJson([
		            'message' => 'Not Found',
		            'status'  => 404
		        ]);
	}
	
	
	/** @test **/
	public function testRouteShouldNotMatchAnInvalidRoute()
	{
		$this->get('/users/this-is-invalid');
		
		$this->assertNotRegExp(
		            '/User not found/',
		            $this->response->getCOntent(),
		            'UserController@show route matching when it should not.'
		        );
	}
	
	
	/** @test **/
	public function testShouldSaveNewUserInDatabase()
	{
		$this->post('/users', [
		            'name' => 'Jonh Smith',
		            'email' => 'jonh@smith.com',
		            'type' => 0,
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		
		$data = $body['data'];
		
		$this->assertEquals('Jonh Smith', $data['name']);
		$this->assertEquals('jonh@smith.com', $data['email']);
		$this->assertEquals(0, $data['type']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('users', ['name' => 'Jonh Smith']);
	}
	
	
	/** @test **/
	public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
	{
		$this->post('/users', [
		            'name' => 'Jonh Smith',
		            'email' => 'jonh@smith.com',
		            'type' => 0,
		        ]);
		
		$this->seeStatusCode(201)
		        ->seeHeaderWithRegExp('Location', '#/users/[\d]+$#');
	}
	
	
	/** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$user = factory('App\User')->create([
		            'name' => 'Jonh Papa',
		            'email' => 'john@papa.com',
		            'type' => 1
		        ]);
        $this->notSeeInDatabase('users', [
                    'name' => 'Jonh Papa 2',
                    'email' => 'john2@papa.com',
                    'type' => 1
                ]);

		
		$this->put("/users/{$user->id}", [
		            'id' => 5,
		            'name' => 'Jonh Papa 2',
		            'email' => 'john2@papa.com',
		            'type' => 1
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'id' => 1,
		            'name' => 'Jonh Papa 2',
		            'email' => 'john2@papa.com',
		            'type' => 1
		        ])->seeInDatabase('users', ['name' => 'Jonh Papa 2']);

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
		        ->put('/users/999999999999999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'User not found'
		            ] 
		        ]);
		
	}
	
	
	/** @test **/
	public function testUpdateShouldNotMatchInvalidRoute()
	{
		$this->put('/users/this-is-invalid')->seeStatusCode(404);
	}
	
	
	/** @test **/
	public function testDestroyShouldRemoveValidUser()
	{
		$user = factory('App\User')->create();
		$this
		        ->delete("/users/{$user->id}")
		        ->seeStatusCode(204)
		        ->isEmpty();
		$this->notSeeInDatabase('users', ['id' => $user->id]);
	}
	
	/** @test **/
	public function testDestroyShouldReturn404WithInvalidId()
	{
		$this
		        ->delete('/users/99999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'User not found'
		        ] ]);
	}
	
	/** @test **/
	public function testDestroyShouldNotMatchInvalidRoute()
	{
		$this->delete('/users/this-is-invalid')->seeStatusCode(404);
	}
	
	/** @test **/
	public function showOptionallyIncludesEvents()
	{
		$event = $this->eventFactory();
		$user = $event->user;

		$this->get(
			"/users/{$user->id}?include=events", ['Accept'=>'application/json']
		);

		$body = json_decode($this->response->getContent(), true);

		$this->assertARrayHasKey('data', $body);
		$data = $body['data'];
		$this->assertARrayHasKey('events', $data);
		$this->assertArrayHasKey('data', $data['events']);
		$this->assertCount(1, $data['events']['data']);

		// See User Data
		$this->seeJson([
			'id' => $user->id,
			'name' => $user->name,
		]);

		// Test included book Data (the first record)
		$actual = $data['events']['data'][0];
		$this->assertEquals($event->id, $actual['id']);
		$this->assertEquals($event->title, $actual['title']);
		$this->assertEquals($event->description, $actual['description']);
		$this->assertEquals(
			$event->created_at->toIso8601String(),
			$actual['created_at']
		);
		$this->assertEquals(
			$event->updated_at->toIso8601String(),
			$actual['updated_at']
		);

	}
	
	
	
	
}
