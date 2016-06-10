<?php

use App\User;
use App\Transformer\UserTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTransformerTest extends TestCase
{
	use DatabaseMigrations;
	
	
	/** @test **/
	public function testItCanBeInitialized()
	 {
		$subject = new UserTransformer();
		$this->assertInstanceOf(TransformerAbstract::class, $subject);
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
}
