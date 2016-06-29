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
    protected $fillable = ['name', 'email', 'type', 'gender', 'birthday','language','image','password','is_active','activated_at','is_customized','is_demo_viewed','activation_code','last_login','description','eventbrite_key','meetup_key','ionic_id','website','phone', 'facebook_id','google_id','country','state','city','zip_code','company_logo','company_pitch','newsletter','community_size'];

    public function events() {
        return $this->hasMany(Event::class); 
    }
}
