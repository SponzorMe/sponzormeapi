<?php

namespace Tests\Controllers;


use TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class TasksControllerTest extends TestCase
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
    public function testShouldReturnAValidTaskType()
    {
        $task = $this->taskFactory();

        $this->get("/tasks/{$task->id}", ['Accept'=> 'application/json']);
        $this->seeStatusCode(200);
		
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];

        // Check tag propierties exist in the response
        $this->assertEquals($task->id, $data['id']);
        $this->assertEquals($task->text, $data['text']);
        $this->assertEquals($task->owner_id, $data['owner_id']);
        $this->assertEquals($task->sponsorship_id, $data['sponsorship_id']);
        $this->assertEquals($task->status, $data['status']);
    }

    public function testShouldSaveNewTaskTypeInDatabase()
	{
		$task_type = $this->taskFactory();
        $user = factory(\App\User::class)->create();

        $this->notSeeInDatabase('tasks', ['text' => 'My Fake Task Text']);
		
		$this->post('/tasks', [
		            'text' => 'My Fake Task Text',
					'sponsorship_id' => $task_type->id,
                    'owner_id' => $user->id
		        ]);
		
		$body = json_decode($this->response->getContent(), true);
		$this->assertArrayHasKey('data', $body);
		$data = $body['data'];
		
		$this->assertEquals('My Fake Task Text', $data['text']);
		$this->assertEquals($task_type->id, $data['sponsorship_id']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');

        $this->assertArrayHasKey('created_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created_at']);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated_at']);
		
		$this->seeInDatabase('tasks', ['text' => 'My Fake Task Text']);
	}

    /** @test **/
	public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
	{
		$task_type = $this->taskFactory();
        $user = factory(\App\User::class)->create();

        $this->notSeeInDatabase('tasks', ['text' => 'My Fake Task Text']);
		
		$this->post('/tasks', [
		            'text' => 'My Fake Task Text',
					'sponsorship_id' => $task_type->id,
                    'owner_id' => $user->id
		        ]);
		
		$this->seeStatusCode(201)->seeHeaderWithRegExp('Location', '#/tasks/[\d]+$#');
	}

    /** @test **/
	public function testUpdateShouldOnlyChangeFillableFields()
	{
		$task_type = $this->taskFactory();
        $user = factory(\App\User::class)->create();

		$task = factory('App\Task')->create([
		            'text' => 'My Fake Task Text',
		            'sponsorship_id' => $task_type->id,
                    'owner_id' => $user->id
		        ]);
        $this->notSeeInDatabase('tasks', [
                    'text' => 'My Fake Task Text 2',
		            'sponsorship_id' => $task_type->id,
                    'owner_id' => $user->id
                ]);

		
		$this->put("/tasks/{$task->id}", [
		            'text' => 'My Fake Task Text 2',
		            'sponsorship_id' => $task_type->id,
                    'owner_id' => $user->id
		        ]);
		$this->seeStatusCode(200)->seeJson([
		            'text' => 'My Fake Task Text 2',
		            'sponsorship_id' => $task_type->id,
                    'owner_id' => $user->id
		        ])->seeInDatabase('tasks', ['text' => 'My Fake Task Text 2']);

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
		        ->put('/tasks/999999999999999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Task not found'
		            ] 
		        ]);
		
	}
	
	
	/** @test **/
	public function testUpdateShouldNotMatchInvalidRoute()
	{
		$this->put('/tasks/this-is-invalid')->seeStatusCode(404);
	}

    /** @test **/
	public function testDestroyShouldRemoveValidTaskType()
	{
		$task = $this->taskFactory();
		$this
		        ->delete("/tasks/{$task->id}")
		        ->seeStatusCode(204)
		        ->isEmpty();
		$this->notSeeInDatabase('tasks', ['id' => $task->id]);
	}
	
	/** @test **/
	public function testDestroyShouldReturn404WithInvalidId()
	{
		$this
		        ->delete('/tasks/99999')
		        ->seeStatusCode(404)
		        ->seeJsonEquals([
		            'error' => [
		                'message' => 'Task not found'
		        ] ]);
	}
	
	/** @test **/
	public function testDestroyShouldNotMatchInvalidRoute()
	{
		$this->delete('/tasks/this-is-invalid')->seeStatusCode(404);
	}
}
