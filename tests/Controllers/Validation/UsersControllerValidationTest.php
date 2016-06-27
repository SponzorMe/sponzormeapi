<?php 

namespace Tests\Controllers\Validation;
use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class UsersControllerValidationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function testItValidateRequiredFueldWhenCreateNewUser()
    {
        $this->post('/users', [], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The name field is required."], $body['name']);
        $this->assertEquals(["The email field is required."], $body['email']);
        $this->assertEquals(["The type field is required."], $body['type']);
    }

    /** @test **/
    public function testItValidateRequiredFueldWhenUpdateNewUser()
    {
        $user = factory('App\User')->create();
        $this->put("/users/{$user->id}", [], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The name field is required."], $body['name']);
        $this->assertEquals(["The email field is required."], $body['email']);
        $this->assertEquals(["The type field is required."], $body['type']);
    }

    /** @test **/
    public function testNameFailsCreateValidationWhenJustTooLong()
    {
        $user = factory('App\User')->create();
        $user->name = str_repeat('a', 256);
        $this->post("/users", [
            'name'=>$user->name,
            'email' =>$user->email,
            'type' =>$user->type
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The name may not be greater than 255 characters."], $body['name']);

        
        $this->notSeeInDatabase('users', ['name' => $user->name]);
    }

    /** @test **/
    public function testNameFailsUpdateValidationWhenJustTooLong()
    {
        $user = factory('App\User')->create();
        $user->name = str_repeat('a', 256);
        $this->put("/users/{$user->id}", [
            'name'=>$user->name,
            'email' =>$user->email,
            'type' =>$user->type
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true);

        $this->assertEquals(["The name may not be greater than 255 characters."], $body['name']);

        $this->notSeeInDatabase('users', ['name' => $user->name]);
    }

    /** @test **/
    public function testNameFailsCreateValidationWhenExactlyMax()
    {
        $user = factory('App\User')->create();
        $user->name = str_repeat('a', 255);
        $this->post("/users", [
            'name'=>$user->name,
            'email' =>$user->email,
            'type' =>$user->type
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_CREATED, $this->response->getStatusCode());
        $this->seeInDatabase('users', ['name' => $user->name]);
    }

    /** @test **/
    public function testNameFailsUpdateValidationWhenExactlyMax()
    {
        $user = factory('App\User')->create();
        $user->name = str_repeat('a', 255);
        $this->put("/users/{$user->id}", [
            'name'=>$user->name,
            'email' =>$user->email,
            'type' =>$user->type
        ], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
        $this->seeInDatabase('users', ['name' => $user->name]);
    }
}