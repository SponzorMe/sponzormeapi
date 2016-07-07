<?php

namespace Tests\Transformers;
use TestCase;
use App\Sponsorship;
use App\Transformer\SponsorshipTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Carbon\Carbon;

class SponsorshipTransformerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->subject = new SponsorshipTransformer();
    }
	
	/** @test **/
	public function testItCanBeInitialized()
	{
		$subject = new SponsorshipTransformer();
		$this->assertInstanceOf(TransformerAbstract::class, $subject);
	}

    /** @test **/
    public function testItTransformsSponsorshipModel()
    {
        $sponsorship = $this->sponsorshipFactory();
        $subject = new SponsorshipTransformer();
        $transform = $subject->transform($sponsorship);
        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('cause', $transform);
        $this->assertArrayHasKey('sponsorship_type_id', $transform);
        $this->assertArrayHasKey('sponsor_id', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);
    }
}