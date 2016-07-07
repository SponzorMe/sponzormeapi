<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['text', 'owner_id', 'sponsorship_id', 'status', 'type'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id'); 
    }
    public function sponsorship() {
        return $this->belongsTo(Sponsorship::class, 'sponsorship_id'); 
    }
}