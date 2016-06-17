<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    use Rateable;
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['name', 'email', 'type'];

    public function events() {
        return $this->hasMany(Event::class); 
    }
}
