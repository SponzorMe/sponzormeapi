<?php

namespace App\Transformer;

use App\Event;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{
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
             'created_at'   => $event->created_at->toIso8601String(),
             'updated_at'   => $event->updated_at->toIso8601String(),
             'released'     => $event->created_at->diffForHumans()
         ];
     }
}