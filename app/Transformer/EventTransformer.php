<?php

namespace App\Transformer;

use App\Event;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class EventTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['tags', 'type', 'organizer', 'sponsorship_types'];
    
    public function includeTags(Event $event)
    {
        return $this->collection($event->tags, new TagTransformer());
    }

    public function includeType(Event $event)
    {
        return $this->item($event->type, new TypeTransformer());
    }

    public function includeOrganizer(Event $event)
    {
        return $this->item($event->user, new UserTransformer());
    }

    public function includeSponsorshipTypes(Event $event)
    {
        return $this->collection($event->sponsorshipTypes, new SponsorshipTypeTransformer());
    }
    
    /**
     * Transform a Event model into an array
     * 
     * @param Event $event
     * @return array
     */
     public function transform(Event $event)
     {
         return [
             'id'           => $event->id,
             'title'        => $event->title,
             'summary'      => $event->summary,
             'description'  => $event->description,
             'organizer'    => $event->user,
             'image'        => $event->image,
             'language'     => $event->language,
             'is_private'       => ($event->is_private) ? true : false,
             'is_outstanding'   => ($event->is_outstanding) ? true : false,
             'country'      => $event->country,
             'place_name'   => $event->place_name,
             'place_id'     => $event->place_id,
             'latitude'     => $event->latitude,
             'longitude'    => $event->longitude,
             'address'      => $event->address,
             'timezone'     => $event->timezone,
             'end'          => Carbon::parse($event->end)->toIso8601String(),
             'start'        => Carbon::parse($event->start)->toIso8601String(),
             'duration'     => Carbon::parse($event->start)->diffInHours(Carbon::parse($event->end)).' Hours',
             'created_at'   => $event->created_at->toIso8601String(),
             'updated_at'   => $event->updated_at->toIso8601String(),
             'released'     => $event->created_at->diffForHumans()
         ];
     }
}