<?php
namespace Tests\Transformers;
use TestCase;
use App\SponsorshipType;
use App\Transformer\SponsorshipTypeTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class SponsorshipTypeTransformerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->subject = new SponsorshipTypeTransformer();
    }
	
	
	/** @test **/
	public function testItCanBeInitialized()
	 {
		 $this->assertInstanceOf(SponsorshipTypeTransformer::class, $this->subject);
	}

    /** @test **/
    public function testItTransformsSponsorshipTypeModel()
    {
        $sponsorshipType=$this->sponsorshipTypeFactory();

        $subject = new SponsorshipTypeTransformer();

        $transform = $subject->transform($sponsorshipType);

        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('kind', $transform);
        $this->assertArrayHasKey('cost', $transform);
        $this->assertArrayHasKey('total_slots', $transform);
        $this->assertArrayHasKey('available_slots', $transform);
        $this->assertArrayHasKey('used_slots', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);

        $this->assertEquals($sponsorshipType->id, $transform['id']);
        $this->assertEquals($sponsorshipType->kind, $transform['kind']);
        $this->assertEquals($sponsorshipType->cost, $transform['cost']);
        $this->assertEquals($sponsorshipType->total_slots, $transform['total_slots']);
        $this->assertEquals($sponsorshipType->total_slots-$sponsorshipType->used_slots, $transform['available_slots']);
        $this->assertEquals($sponsorshipType->used_slots, $transform['used_slots']);
        $this->assertEquals($sponsorshipType->created_at->toIso8601String(), $transform['created_at']);
        $this->assertEquals($sponsorshipType->updated_at->toIso8601String(), $transform['updated_at']);
    }
}
