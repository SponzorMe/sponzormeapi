<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Tag extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['title', 'description'];

    public function events() {
        return $this->belongsToMany(Event::class); 
    }
}
