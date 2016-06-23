<?php

namespace App\Transformer;

use App\Tag;
use League\Fractal\TransformerAbstract;

/**
 * Class BundleTransformer
 * @package App\Transformer
 */
class TagTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['events'];
    /**
     * Include a bundle's books
     * @param Bundle $bundle
     * @return \League\Fractal\Resource\Collection
     */
    public function includeEvents(Tag $tag) 
    {
        return $this->collection($tag->events, new EventTransformer()); 
    }
    /**
     * Transform a Tag model into an array
     * 
     * @param Tag $tag
     * @return array
     */
     public function transform(Tag $tag)
     {
         return [
            'id' => $tag->id,
            'title' => $tag->title,
            'description' => $tag->description,
            //'created_at' => $tag->created_at->toIso8601String(), 
            //'updated_at' => $tag->updated_at->toIso8601String()
         ];
     }

     public function removeEvent($tagId, $eventId) {
        $tag = \App\Tag::findOrFail($tagId);
        $event = \App\Event::findOrFail($eventId);
        $tag->events()->detach($event); return response(null, 204);
    }
}