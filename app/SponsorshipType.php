<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SponsorshipType extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['kind', 'cost', 'total_slots', 'used_slots', 'event_id'];

    public function event() {
        return $this->belongsTo(Event::class); 
    }
    public function perks(){
        return $this->hasMany(Perk::class);
    }
}