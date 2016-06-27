<?php 
namespace Tests\Transformers;
use TestCase;
use App\Transformer\RatingTransformer;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RatingTransformerTest extends TestCase{
	use DatabaseMigrations;
	
	/**
	* @var RatingTransformer
	*/
	private $subject;
	
	public function setUp() {
		parent::setUp();
		$this->subject = new RatingTransformer();
	}
	
	/** @test **/
	public function testItCanBeInitialized() {
		$this->assertInstanceOf(RatingTransformer::class, $this->subject);
	}
	
	/** @test **/
	public function testItCanTransforRatingForAnUser() {
		$user = factory(\App\User::class)->create();
		$rating = $user->ratings()->save(
		        factory(\App\Rating::class)->make()
		    );
		$actual = $this->subject->transform($rating);
		$this->assertEquals($rating->id, $actual['id']);
		$this->assertEquals($rating->value, $actual['value']);
		$this->assertEquals($rating->rateable_type, $actual['type']);
		$this->assertEquals(
		        $rating->created_at->toIso8601String(),
		        $actual['created_at']
		    );
		$this->assertEquals(
		        $rating->updated_at->toIso8601String(),
		        $actual['updated_at']
		);
		$this->assertArrayHasKey('links', $actual);
		$links = $actual['links'];
		$this->assertCount(1, $links);
		$userLink = $links[0];
		$this->assertArrayHasKey('rel', $userLink);
		$this->assertEquals('user', $userLink['rel']);
		$this->assertArrayHasKey('href', $userLink);
		$this->assertEquals(route('users.show', ['id' => $user->id]), $userLink['href']);
	}
	/**
	* @test
	* @expectedException \RuntimeException
	* @expectedExceptionMessage Rateable model type for Foo\Bar is not defined
	*/
	public function testItThrowsAnExceptionWhenModelIsNotDefined() {
		$rating = factory(\App\Rating::class)->create([
			'value' => 5,
			'rateable_type' => 'Foo\Bar',
			'rateable_id' => 1
		]);
		$this->subject->transform($rating);
	}
}
