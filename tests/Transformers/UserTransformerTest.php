<?php
namespace Tests\Transformers;
use TestCase;
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
        $user->ratings()->save(factory(\App\Rating::class)->make(['value' => 3]));
        $user->ratings()->save(factory(\App\Rating::class)->make(['value' => 5]));
        $subject = new UserTransformer();

        $transform = $subject->transform($user);

        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('name', $transform);
        $this->assertArrayHasKey('email', $transform);
        $this->assertArrayHasKey('type', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);

        $this->assertEquals($user->id, $transform['id']);
        $this->assertEquals($user->name, $transform['name']);
        $this->assertEquals($user->email, $transform['email']);
        $this->assertEquals($user->type, $transform['type']);
        $this->assertEquals($user->created_at->toIso8601String(), $transform['created_at']);
        $this->assertEquals($user->updated_at->toIso8601String(), $transform['updated_at']);

        // Rating
        $this->assertArrayHasKey('rating', $transform);
        $this->assertInternalType('array', $transform['rating']);
        $this->assertEquals(4, $transform['rating']['average']);
        $this->assertEquals(5, $transform['rating']['max']);
        $this->assertEquals(80, $transform['rating']['percent']);
        $this->assertEquals(2, $transform['rating']['count']);
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
