<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TagsTableSeeder extends Seeder
{
    public function run(){
        factory(App\Tag::class, 5)->create()->each(function ($tag) { 
            $eventsCount = rand(2, 5);
            $eventsIds = [];
            while ($eventsCount > 0) { 
                $event = \App\Event::whereNotIn('id', $eventsIds)
                ->orderByRaw("RAND()")
                ->first();
                $tag->events()->attach($event);
                $eventsIds[] = $event->id;
                $eventsCount--;
            }
        });
    }
}