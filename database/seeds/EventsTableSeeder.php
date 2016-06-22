<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EventsTableSeeder extends Seeder
{
	public function run(){
		$users = factory(App\User::class, 10)->create();

		$users->each(function ($user) {
			$user->ratings()->saveMany(factory(App\Rating::class, rand(20, 50))->make());
			$eventsCount = rand(1, 5);
			while ($eventsCount > 0) {
				$event = factory(App\Event::class)->make();
				$user->events()->save($event);
				$tags = factory(\App\Tag::class, rand(1,5))->create();
				$tags->each(function ($tag, $event) {
					$tag->events()->attach($event);
				});
				$event->ratings()->saveMany(
				                factory(App\Rating::class, rand(20, 50))->make()
				            );
				$eventsCount--;
			}
		}
		);
	}
}
