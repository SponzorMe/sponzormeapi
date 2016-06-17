<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['title', 'description', 'summary', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class); 
    }
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
}
