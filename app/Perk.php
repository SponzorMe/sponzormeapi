<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Perk extends Model
{
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['title', 'description', 'sponsorship_type_id'];

    public function sponsorshipType() {
        return $this->belongsTo(SponsorshipType::class, 'sponsorship_type_id'); 
    }
}