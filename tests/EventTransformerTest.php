<?php

use App\Event;
use App\Transformer\EventTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class EventTransformerTest extends TestCase
{
	use DatabaseMigrations;
	
	
	/** @test **/
	public function testItCanBeInitialized()
	 {
		$subject = new EventTransformer();
		$this->assertInstanceOf(TransformerAbstract::class, $subject);
	}

    /** @test **/
    public function testItTransformsEventModel()
    {
        $event = $this->eventFactory();
        $subject = new EventTransformer();

        $transform = $subject->transform($event);

        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('title', $transform);
        $this->assertArrayHasKey('description', $transform);
        $this->assertArrayHasKey('summary', $transform);
        $this->assertArrayHasKey('organizer', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);
    }
}