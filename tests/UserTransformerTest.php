<?php

use App\User;
use App\Transformer\UserTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTransformerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->subject = new UserTransformer();
    }
	
	
	/** @test **/
	public function testItCanBeInitialized()
	 {
		 $this->assertInstanceOf(UserTransformer::class, $this->subject);
	}

    /** @test **/
    public function testItTransformsUserModel()
    {
        $user = factory(User::class)->create();
        $subject = new UserTransformer();

        $transform = $subject->transform($user);

        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('name', $transform);
        $this->assertArrayHasKey('email', $transform);
        $this->assertArrayHasKey('type', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);
    }

    /** @test **/
    public function testItCanTransformRelatedEvents()
    {
        $event = $this->eventFactory();
        $user = $event->user;
        $data = $this->subject->includeEvents($user);
        $this->assertInstanceOf(\League\Fractal\Resource\Collection::class, $data);
    }
}
