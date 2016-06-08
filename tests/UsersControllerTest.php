<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class UsersControllerTest extends TestCase
{
    /** @test **/
    public function testAllUsers()
    {
        $this->get('/users')
        ->seeJson([
                'id' => 1,
                'name' => 'Sebastian Gomez',
                'email' => 'seagomezar@gmail.com',
                'type' => 1
        ])
        ->seeStatusCode(200);
    }

    /** @test **/
    public function testShouldReturnValidUser()
    {
        $this->get('/users/1')
        ->seeStatusCode(200)
        ->seeJson([
            'id' => 1,
            'name' => 'Sebastian Gomez',
            'email' => 'seagomezar@gmail.com',
            'type' => 1
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
        $this->notSeeInDatabase('users', [
            'name' => 'Jonh Papa'
        ]);

        $this->put('users/1', [
            'id' => 5,
            'name' => 'Jonh Papa',
            'email' => 'john@papa.com',
            'type' => 1
        ]);
        $this->seeStatusCode(200)->seeJson([
            'id' => 1,
            'name' => 'Jonh Papa',
            'email' => 'john@papa.com',
            'type' => 1
        ])->seeInDatabase('users', ['name' => 'Jonh Papa']);
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
    public function testDestroyShouldRemoveValidBook()
    {
        $this
        ->delete('/users/1')
        ->seeStatusCode(204)
        ->isEmpty();
        $this->notSeeInDatabase('users', ['id' => 1]);
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
