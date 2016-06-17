<?php

use App\Tag;
use App\Transformer\TagTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TagTransformerTest extends TestCase
{
	use DatabaseMigrations;

    
    /**
     * @var BundleTransformer
     */
	private $subject;

    public function setUp(){
        parent::setUp();
        $this->subject = new TagTransformer();
    }
	
	/** @test **/
	public function testItCanBeInitialized()
	{
		$this->assertInstanceOf(TagTransformer::class, $this->subject);
	}

    /** @test **/
    public function testItTransformsTagModel()
    {
        $tag = factory(\App\Tag::class)->create();

        $actual = $this->subject->transform($tag);

        $this->assertEquals($tag->id, $actual['id']);

        $this->assertEquals($tag->title, $actual['title']);

        $this->assertEquals($tag->description, $actual['description']);

        $this->assertEquals($tag->created_at->toIso8601String(), $actual['created_at']);

        $this->assertEquals($tag->updated_at->toIso8601String(), $actual['updated_at']);
    }

    /** @test **/
	public function testItCanTransformRelatedEvents()
	{
        $tag = $this->tagFactory();
        $data = $this->subject->includeEvents($tag);
        $this->assertInstanceOf(League\Fractal\Resource\Collection::class, $data);
        $this->assertInstanceOf(\App\Event::class, $data->getData()[0]);
		$this->assertCount(2, $data->getData());
	}

}