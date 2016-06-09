<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UsersControllerTest extends TestCase
{
    use DatabaseMigrations;
    /** @test **/
    public function testShouldReturnCollectionOfUsers()
    {
        $users = factory('App\User', 2)->create();
        $this->get('/users');
        
        foreach ($users as $user) {
            $this->seeJson(['name'=> $user->name]);
        }
    }

    /** @test **/
    public function testShouldReturnValidUser()
    {
        $user = factory('App\User')->create();
        $this->get("/users/{$user->id}")
        ->seeStatusCode(200)
        ->seeJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'type' => $user->type
        ]);
        $data = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    /** @test **/
    public function testShouldFailWhenUserIdDoesNotExist()
    {
        $this->get('/users/999999')
        ->seeStatusCode(404)
        ->seeJson([
            'error' => [
                'message'=> 'User not found'
            ]
        ]);
    }

    /** @test **/
    public function testRouteShouldNotMatchAnInvalidRoute()
    {
        $this->get('/users/this-is-invalid');

        $this->assertNotRegExp(
            '/User not found/',
            $this->response->getCOntent(),
            'UserController@show route matching when it should not.'
        );
    }

    /** @test **/
    public function testShouldSaveNewBookInDatabase()
    {
        $this->post('/users', [
            'name' => 'Jonh Smith',
            'email' => 'jonh@smith.com',
            'type' => 0,

        ]);
        $this->seeJson(['created' => true])->seeInDatabase('users', ['name' => 'Jonh Smith']);
    }

    /** @test **/
    public function testShouldRespondWith201AndLocationHeaderWhenSuccessful()
    {
        $this->post('/users', [
            'name' => 'Jonh Smith',
            'email' => 'jonh@smith.com',
            'type' => 0,
        ]);

        $this->seeStatusCode(201)
        ->seeHeaderWithRegExp('Location', '#/users/[\d]+$#');
    }

    /** @test **/
    public function testUpdateShouldOnlyChangeFillableFields()
    {
        $user = factory('App\User')->create([
            'name' => 'Jonh Papa',
            'email' => 'john@papa.com',
            'type' => 1
        ]);

        $this->put("/users/{$user->id}", [
            'id' => 5,
            'name' => 'Jonh Papa 2',
            'email' => 'john2@papa.com',
            'type' => 1
        ]);
        $this->seeStatusCode(200)->seeJson([
            'id' => 1,
            'name' => 'Jonh Papa 2',
            'email' => 'john2@papa.com',
            'type' => 1
        ])->seeInDatabase('users', ['name' => 'Jonh Papa 2']);
    }

    /** @test **/
    public function testUpdateShouldFailWithInvalidId()
    {
        $this
        ->put('/users/999999999999999')
        ->seeStatusCode(404)
        ->seeJsonEquals([
            'error' => [
                'message' => 'User not found'
            ] 
        ]);

    }

    /** @test **/
    public function testUpdateShouldNotMatchInvalidRoute()
    {
        $this->put('/users/this-is-invalid')->seeStatusCode(404);
    }

    /** @test **/
    public function testDestroyShouldRemoveValidUser()
    {
        $user = factory('App\User')->create();
        $this
        ->delete("/users/{$user->id}")
        ->seeStatusCode(204)
        ->isEmpty();
        $this->notSeeInDatabase('users', ['id' => $user->id]);
    }
    /** @test **/
    public function testDestroyShouldReturn404WithInvalidId()
    {
        $this
        ->delete('/users/99999')
        ->seeStatusCode(404)
        ->seeJsonEquals([
            'error' => [
                'message' => 'User not found'
        ] ]);
    }
    /** @test **/
    public function testDestroyShouldNotMatchInvalidRoute()
    {
        $this->delete('/books/this-is-invalid')->seeStatusCode(404);
    }





}
