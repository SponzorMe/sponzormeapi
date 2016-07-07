<?php

namespace Tests\Transformers;
use TestCase;
use App\Task;
use App\Transformer\TaskTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Carbon\Carbon;

class TaskTransformerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->subject = new TaskTransformer();
    }
	
	/** @test **/
	public function testItCanBeInitialized()
	{
		$subject = new TaskTransformer();
		$this->assertInstanceOf(TransformerAbstract::class, $subject);
	}

    /** @test **/
    public function testItTransformsTaskModel()
    {
        $task = $this->taskFactory();
        $subject = new TaskTransformer();
        $transform = $subject->transform($task);
        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('owner_id', $transform);
        $this->assertArrayHasKey('sponsorship_id', $transform);
        $this->assertArrayHasKey('text', $transform);
        $this->assertArrayHasKey('status', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);
    }
}