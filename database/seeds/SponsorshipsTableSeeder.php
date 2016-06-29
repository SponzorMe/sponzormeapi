<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SponsorshipTableSeeder extends Seeder
{
	public function run(){
        
		$eventsCount = 5;
        $sponsors = factory('App\User', 5)->create(['type' => 'sponsor']); 
        $sponsors->each(function ($sponsor) {
            $eventsCount = 5;
            $organizer = factory('App\User')->create(['type' => 'organizer']);
            while ($eventsCount > 0) {
                $event = factory(App\Event::class)->make();
                $organizer->events()->save($event);
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
                    $sponsorship = factory(App\Sponsorship::class)->make();
                    $sponsorship->sponsor()->associate($sponsor);
                    $sponsorship->sponsorshipType()->associate($sponsorshipType);
                    $sponsorship->save();
                    $sponsorshipType->used_slots = $sponsorshipType->used_slots+1;
                    $sponsorshipType->save();
                }
                $eventsCount--;
            }
        });
	}
}


