<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    public function run(){
        DB::table('users')->insert([
            'name' => 'Sebastian Gomez',
            'email' => 'seagomezar@gmail.com',
            'type' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}