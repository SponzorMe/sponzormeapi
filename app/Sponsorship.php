<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Sponsorship extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['cause', 'sponsor_id', 'sponsorship_type_id', 'status', 'is_rated_by_sponsor', 'is_rated_by_organizer'];

    public function sponsor() {
        return $this->belongsTo(User::class, 'sponsor_id'); 
    }
    public function sponsorshipType() {
        return $this->belongsTo(SponsorshipType::class, 'sponsorship_type_id'); 
    }
    public function tasks(){
        return $this->hasMany(Task::class); 
    }
}