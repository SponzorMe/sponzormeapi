<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    public function run(){
        factory(App\User::class, 10)->create()->each(function ($user) { 
            $eventsCount = rand(1, 5);
            while ($eventsCount > 0) { 
                $user->events()->save(factory(App\Event::class)->make()); 
                $eventsCount--;
            }
        });
    }
}