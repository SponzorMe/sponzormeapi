<?php

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
    * See if the response has a header.
    *
    * @param $header
    * @return $this
    */
    public function seeHasHeader($header) {
        $this->assertTrue(
            $this->response->headers->has($header),
            "Response should have the header '{$header}' but does not."
        );
        return $this; 
    }
    /**
    * Asserts that the response header matches a given regular expression
    *
    * @param $header
    * @param $regexp
    * @return $this
    */
    public function seeHeaderWithRegExp($header, $regexp) {
        $this->seeHasHeader($header)->assertRegExp($regexp, $this->response->headers->get($header));
        return $this; 
    }
    /**
    * Convenience method for creating a event with an organizer
    *
    * @param int $count
    * @return mixed
    */
    protected function eventFactory($count = 1) {
        $user = factory(\App\User::class)->create();
        $events  = factory(\App\Event::class, $count)->make();
        
        if ($count === 1) { 
            $events->user()->associate($user);
            $tags = factory(\App\Tag::class, rand(1,5))->create();
				$tags->each(function ($tag) {
					$tag->events()->attach($events);
				});
            $events->save();
        }
        else{
            foreach ($events as $event) { 
                $event->user()->associate($user);
                $tags = factory(\App\Tag::class, rand(1,5))->create();
				$tags->each(function ($tag) {
					$tag->events()->attach($event);
				});
                $event->save();
            }
        }

        return $events;
    }
    /**
    * Convenience method for creating a event with tags
    *
    * @param int $count
    * @return mixed
    */
    protected function tagFactory($eventCount = 2) {
        if ($eventCount <= 1) {
            throw new \RuntimeException('A bundle must have two or more books!');
        }
        $tag = factory(\App\Tag::class)->create();
        $events  = $this->eventFactory($eventCount);

        $events->each(function($event) use ($tag){
            $tag->events()->attach($event);
        });

        return $tag;
    }
}
