<?php 

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class EventsControllerValidationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function testItValidateRequiredFieldWhenCreateNewEvent()
    {
        $this->post('/events', [], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The title field is required."], $body['title']);
        $this->assertEquals(["The description field is required."], $body['description']);
        $this->assertEquals(["The summary field is required."], $body['summary']);
        $this->assertEquals(["The user id field is required."], $body['user_id']);
        $this->assertEquals(["The type id field is required."], $body['type_id']);
        $this->assertEquals(["The start field is required."], $body['start']);
        $this->assertEquals(["The end field is required."], $body['end']);
        $this->assertEquals(["The timezone field is required."], $body['timezone']);
    }

    /** @test **/
    public function testItValidateRequiredFieldWhenUpdateNewEvent()
    {
        $event = $this->eventFactory();
        $this->put("/events/{$event->id}", [], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The title field is required."], $body['title']);
        $this->assertEquals(["The description field is required."], $body['description']);
        $this->assertEquals(["The summary field is required."], $body['summary']);
        $this->assertEquals(["The user id field is required."], $body['user_id']);
        $this->assertEquals(["The type id field is required."], $body['type_id']);
        $this->assertEquals(["The start field is required."], $body['start']);
        $this->assertEquals(["The end field is required."], $body['end']);
        $this->assertEquals(["The timezone field is required."], $body['timezone']);
    }

    /** @test **/
    public function testNameFailsCreateValidationWhenJustTooLong()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 256);
        $this->post("/events", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The title may not be greater than 255 characters."], $body['title']);

        
        $this->notSeeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testNameFailsUpdateValidationWhenJustTooLong()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 256);
        $this->put("/events/{$event->id}", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The title may not be greater than 255 characters."], $body['title']);

        $this->notSeeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testNameFailsCreateValidationWhenExactlyMax()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 255);
        $this->post("/events", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary,
            'user_id' =>$event->user->id,
            'type_id' =>$event->type->id,
            'start' =>$event->start,
            'end' =>$event->end,
            'timezone' =>$event->timezone
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_CREATED, $this->response->getStatusCode());
        $this->seeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testNameFailsUpdateValidationWhenExactlyMax()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 255);
        $this->put("/events/{$event->id}", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary,
            'user_id' =>$event->user->id,
            'type_id' =>$event->type->id,
            'start' =>$event->start,
            'end' =>$event->end,
            'timezone' =>$event->timezone
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
        $this->seeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testDatesFailsCreateValidationWhenEndBeforeStarts()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 255);
        $this->post("/events", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary,
            'user_id' =>$event->user->id,
            'type_id' =>$event->type->id,
            'start' =>$event->end,
            'end' =>$event->start,
            'timezone' =>$event->timezone
        ], ['Accept' => 'application/json']);
        $body = json_decode($this->response->getContent(), true);    
        $this->assertEquals(["The end must be a date after start."], $body['end']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->notSeeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testDatesFailsUpdateValidationWhenEndBeforeStarts()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 255);
        $this->put("/events/{$event->id}", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary,
            'user_id' =>$event->user->id,
            'type_id' =>$event->type->id,
            'start' =>$event->end,
            'end' =>$event->start,
            'timezone' =>$event->timezone
        ], ['Accept' => 'application/json']);
        $body = json_decode($this->response->getContent(), true);
        $this->assertEquals(["The end must be a date after start."], $body['end']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->notSeeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testDatesFailsCreateValidationWhenEndEqualsStarts()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 255);
        $this->post("/events", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary,
            'user_id' =>$event->user->id,
            'type_id' =>$event->type->id,
            'start' =>$event->start,
            'end' =>$event->start,
            'timezone' =>$event->timezone
        ], ['Accept' => 'application/json']);
        $body = json_decode($this->response->getContent(), true);    
        $this->assertEquals(["The end must be a date after start."], $body['end']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->notSeeInDatabase('events', ['title' => $event->title]);
    }

    /** @test **/
    public function testDatesFailsUpdateValidationWhenEndEqualsStarts()
    {
        $event = $this->eventFactory();
        $event->title = str_repeat('a', 255);
        $this->put("/events/{$event->id}", [
            'title'=>$event->title,
            'description' =>$event->description,
            'summary' =>$event->summary,
            'user_id' =>$event->user->id,
            'type_id' =>$event->type->id,
            'start' =>$event->start,
            'end' =>$event->start,
            'timezone' =>$event->timezone
        ], ['Accept' => 'application/json']);
        $body = json_decode($this->response->getContent(), true);
        $this->assertEquals(["The end must be a date after start."], $body['end']);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());
        $this->notSeeInDatabase('events', ['title' => $event->title]);
    }
}