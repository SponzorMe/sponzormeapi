<?php


namespace Tests\Controllers;
use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UsersRatingsControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function testCanAddRatingToUser()
    {
        $user = factory(\App\User::class)->create();

        $this->post(
            "/users/{$user->id}/ratings",
            ['value' => 5],
            ['Accept' => 'application/json']
        );
        $this->seeStatusCode(201)->seeJson([
            'value'=>5
        ])->seeJson([
            'rel'=>'user',
            'href' => route('users.show', ['id' => $user->id])
        ]);
        $body = $this->response->getData(true);
        $this->assertArrayHasKey('data', $body);

        $data = $body['data'];
        $this->assertArrayHasKey('links', $data);
    }

    /** @test **/
    public function testStoreFailsWhenTheUserIsInvalid() 
    {
        $this->post('/users/1/ratings', [], ['Accept' => 'application/json']);
        $this->seeStatusCode(404);
    }

    /** @test **/
    public function DestroyCanDeleteAnUserRating() 
    {
        $user  = factory(\App\User::class)->create();
        $ratings = $user->ratings()->saveMany(
            factory(\App\Rating::class, 2)->make()
        );
        $this->assertCount(2, $ratings);

        $ratings->each(function (\App\Rating $rating) use ($user) { 
            $this->seeInDatabase('ratings', [
                'rateable_id' => $user->id,
                'id' => $rating->id
            ]);
        });

        $ratingToDelete = $ratings->first();

        $this->delete( "/users/{$user->id}/ratings/{$ratingToDelete->id}")->seeStatusCode(204);

        $dbUser = \App\User::find($user->id);
        $this->assertCount(1, $dbUser->ratings);
        $this->notSeeInDatabase('ratings', ['id' => $ratingToDelete->id]);
    }

    /** @test **/
    public function testDestroyShouldNotDeleteRatingsFromAnotherUser() 
    {
        $users = factory(\App\User::class, 2)->create(); 
        $users->each(function (\App\User $user) {
            $user->ratings()->saveMany( factory(\App\Rating::class, 2)->make()); 
        });
        $firstUser = $users->first();
        $rating = $users->last()->ratings()->first();
        $this->delete( "/users/{$firstUser->id}/ratings/{$rating->id}", [], ['Accept' => 'application/json'])->seeStatusCode(404);
    }

    /** @test **/
    public function destroy_fails_when_the_author_is_invalid() 
    {
        $this->delete('/users/1/ratings/1',[],['Accept' => 'application/json'])->seeStatusCode(404);
    }
}