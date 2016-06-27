<?php

namespace Tests\Controllers;


use TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;

class TagsControllerTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown() {
        parent::tearDown(); 
        Carbon::setTestNow();
    }

    /** @test **/
    public function testShouldReturnAValidTag()
    {
        $tag = $this->tagFactory();

        $this->get("/tags/{$tag->id}", ['Accept'=> 'application/json']);
        $this->seeStatusCode(200);
        $body = $this->response->getData(true);

        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];

        // Check tag propierties exist in the response
        $this->assertEquals($tag->id, $data['id']);
        $this->assertEquals($tag->title, $data['title']);
        $this->assertEquals($tag->description, $data['description']);

        // Check that event data is in the response
        //$this->assertArrayHasKey('events', $data);
        //$events = $data['events'];

        // Check that two events exist in the response
        //$this->assertArrayHasKey('data', $events);
        //$this->assertCount(2, $events['data']);

        // Verify keys for one event...
        // $this->assertEquals(
        //     $tag->events[0]->title,
        //     $events['data'][0]['title']
        // );
        // $this->assertEquals(
        //     $tag->events[0]->description,
        //     $events['data'][0]['description']
        // );
        // $this->assertEquals(
        //     $tag->events[0]->user->toArray(), $events['data'][0]['organizer']
        // );
        // $this->assertEquals(
        //     $tag->events[0]->created_at->toIso8601String(),
        //     $events['data'][0]['created_at']
        // );
        // $this->assertEquals(
        //     $tag->events[0]->updated_at->toIso8601String(),
        //     $events['data'][0]['updated_at']
        // );
    }

    /** @test **/
    public function testAddEventShouldAddAEventToATag()
    {
        $tag = factory(\App\Tag::class)->create();
        $event = $this->eventFactory();

        //Tag should not have any associated event yet
        $this->notSeeInDatabase('event_tag', ['tag_id'=>$tag->id]);

        $this->put("/tags/{$tag->id}/events/{$event->id}", [], ['Accept' => 'application/json']);

        $this->seeStatusCode(200);

        $dbTag = \App\Tag::with('events')->find($tag->id);
        $this->assertCount(1, $dbTag->events, 'The tag should have 1 associated book');

        $this->assertEquals($dbTag->events()->first()->id, $event->id);

        $body = $this->response->getData(true);

        $this->assertArrayHasKey('data', $body);
        //Ensure the event id is in the response.
        // $this->assertArrayHasKey('events', $body['data']);
        // $this->assertArrayHasKey('data', $body['data']['events']);

        //make sure the event is in the response
        // $events = $body['data']['events'];
        // $this->assertEquals($event->id, $events['data'][0]['id']);
    }

    /** @test **/
    public function testAddEventShouldRemoveAEventToATag()
    {
        $tag = $this->tagFactory(3);
        $event = $tag->events()->first();

        $this->seeInDatabase('event_tag',[
            'event_id' => $event->id,
            'tag_id' => $tag->id
        ]);

        $this->assertCount(3, $tag->events);

        $this->delete("/tags/{$tag->id}/events/{$event->id}")->seeStatusCode(204)->notSeeInDatabase('event_tag',[
            'event_id'=>$event->id,
            'tag_id' => $tag->id
        ]);

        $dbTag = \App\Tag::find($tag->id);
        $this->assertCount(2, $dbTag->events);
    }
	
}
