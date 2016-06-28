<?php

namespace Tests\Transformers;
use TestCase;
use App\Perk;
use App\Transformer\PerkTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Carbon\Carbon;

class PerkTransformerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->subject = new PerkTransformer();
    }
	
	/** @test **/
	public function testItCanBeInitialized()
	{
		$subject = new PerkTransformer();
		$this->assertInstanceOf(TransformerAbstract::class, $subject);
	}

    /** @test **/
    public function testItTransformsPerkModel()
    {
        $perk = $this->perkFactory();
        $subject = new PerkTransformer();
        $transform = $subject->transform($perk);
        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('title', $transform);
        $this->assertArrayHasKey('description', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);
    }
}