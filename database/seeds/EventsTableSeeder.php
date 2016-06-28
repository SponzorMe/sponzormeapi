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
				$event->ratings()->saveMany(factory(App\Rating::class, rand(20, 50))->make());
				//adding the sponsorship Type
				$sponsorshipTypesCount = rand(1, 5);
				while ($sponsorshipTypesCount > 0) {
					$sponsorshipType=factory(App\SponsorshipType::class)->make();
					$event->sponsorshipTypes()->save($sponsorshipType);
					//adding some perks to the sponsorship
					$perksCount = rand(2, 6);
					while($perksCount>0){
						$perk = factory(App\Perk::class)->make();
						$sponsorshipType->perks()->save($perk);
						$perksCount--;
					}
					$sponsorshipTypesCount--;
				}
				$eventsCount--;
			}
		});
	}
}
