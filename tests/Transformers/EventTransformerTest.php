<?php




namespace Tests\Transformers;
use TestCase;
use App\Event;
use App\Transformer\EventTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Carbon\Carbon;

class EventTransformerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->subject = new EventTransformer();
    }
	
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
        $this->assertArrayHasKey('image', $transform);
        $this->assertArrayHasKey('language', $transform);
        $this->assertArrayHasKey('is_private', $transform);
        $this->assertArrayHasKey('is_outstanding', $transform);
        $this->assertArrayHasKey('country', $transform);
        $this->assertArrayHasKey('place_name', $transform);
        $this->assertArrayHasKey('place_id', $transform);
        $this->assertArrayHasKey('latitude', $transform);
        $this->assertArrayHasKey('longitude', $transform);
        $this->assertArrayHasKey('address', $transform);
        $this->assertArrayHasKey('timezone', $transform);

        $this->assertArrayHasKey('start', $transform);
        $this->assertArrayHasKey('end', $transform);
        $this->assertArrayHasKey('duration', $transform);
        $this->assertArrayHasKey('released', $transform);

        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);

    }
}