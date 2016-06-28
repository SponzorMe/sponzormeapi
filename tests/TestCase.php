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
            $events->save();
        }
        else{
            foreach ($events as $event) { 
                $event->user()->associate($user);
                $event->save();
            }
        }

        return $events;
    }
    /**
    * Convenience method for creating an sponsorship type with an event
    *
    * @param int $count
    * @return mixed
    */
    protected function sponsorshipTypeFactory($count = 1) {
        $event  = $this->eventFactory();
        $sponsorshipTypes  = factory(\App\SponsorshipType::class, $count)->make();
        if ($count === 1) { 
            $sponsorshipTypes->event()->associate($event);
            $sponsorshipTypes->save();
        }
        else{
            foreach ($sponsorshipTypes as $sponsorshipType) { 
                $sponsorshipType->event()->associate($event);
                $sponsorshipType->save();
            }
        }

        return $sponsorshipTypes;
    }
    /**
    * Convenience method for creating an Perk type with an sponsorship type
    *
    * @param int $count
    * @return mixed
    */
    protected function perkFactory($count = 1) {
        $sponsorshipType  = $this->sponsorshipTypeFactory();
        $perks  = factory(\App\Perk::class, $count)->make();
        if ($count === 1) { 
            $perks->sponsorshipType()->associate($sponsorshipType);
            $perks->save();
        }
        else{
            foreach ($perks as $perk) {
                $perk->sponsorshipType()->associate($sponsorshipType);
                $perk->save();
            }
        }

        return $perks;
    }
    /**
    * Convenience method for creating a event with tags
    *
    * @param int $count
    * @return mixed
    */
    protected function tagFactory($eventCount = 2) {
        if ($eventCount <= 1) {
            throw new \RuntimeException('A tag must have two or more events!');
        }
        $tag = factory(\App\Tag::class)->create();
        $events  = $this->eventFactory($eventCount);

        $events->each(function($event) use ($tag){
            $tag->events()->attach($event);
        });

        return $tag;
    }
}
