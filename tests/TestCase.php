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
    * Convenience method for creating an Sponsorship with an sponsorship type
    *
    * @param int $count
    * @return mixed
    */
    protected function sponsorshipFactory($count = 1) {
        $sponsorshipType  = $this->sponsorshipTypeFactory();
        $sponsor = factory(\App\User::class)->create([
            "type"=>1
        ]);
        $sponsorships  = factory(\App\Sponsorship::class, $count)->make();
        if ($count === 1) { 
            $sponsorships->sponsorshipType()->associate($sponsorshipType);
            $sponsorships->sponsor()->associate($sponsor);
            $sponsorships->save();
        }
        else{
            foreach ($sponsorships as $sponsorship) {
                $sponsorship->sponsorshipType()->associate($sponsorshipType);
                $sponsorship->sponsor()->associate($sponsor);
                $sponsorship->save();
            }
        }

        return $sponsorships;
    }
    /**
    * Convenience method for creating an Task with an sponsorship and Owner
    *
    * @param int $count
    * @return mixed
    */
     protected function taskFactory($count = 1) {
        $sponsorship = $this->sponsorshipFactory();
        $user = factory(\App\User::class)->create();
        $tasks = factory(App\Task::class, $count)->create([
            'type'=>$user->type,
            'owner_id' => $user->id,
            'sponsorship_id'=> $sponsorship->id
        ]);

        return $tasks;
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
