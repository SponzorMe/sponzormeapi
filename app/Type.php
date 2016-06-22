<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Type extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['title', 'description'];

    public function events() {
        return $this->hasMany(Event::class); 
    }
}
