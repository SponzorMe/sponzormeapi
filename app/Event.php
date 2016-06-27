<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    use Rateable;
    /**
    * The attributes that are mass assignable
    *
    * @var array
    */
    protected $fillable = ['title', 'description', 'summary', 'user_id', 'type_id', 'image', 'language', 'is_private', 'is_outstanding', 'country', 'place_name', 'place_id', 'latitude', 'longitude', 'address', 'start', 'end', 'timezone'];

    public function user() {
        return $this->belongsTo(User::class); 
    }
    public function type() {
        return $this->belongsTo(Type::class); 
    }
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
    public function sponsorshipTypes(){
        return $this->hasMany(SponsorshipType::class);
    }
}
