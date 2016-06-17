<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Transformer\TagTransformer;

/**
 * Class TagsController
 * @package App\Http\Controllers
 */
class TagsController extends Controller
{
    public function show($id)
    {
       $tag = Tag::findOrFail($id);
       $data = $this->item($tag, new TagTransformer());

       return response()->json($data);
    }
    /**
    * @param int $tagId
    * @param int $eventId
    * @return \Illuminate\Http\JsonResponse
    */
    public function addEvent($tagId, $eventId)
    {
        $tag = Tag::findOrFail($tagId);
        $event = \App\Event::findOrFail($eventId);

        $tag->events()->attach($event);

        $data = $this->item($tag, new TagTransformer());

        return response()->json($data);
    }

    public function removeEvent($tagId, $eventId) {
        $tag = Tag::findOrFail($tagId);
        $event = \App\Event::findOrFail($eventId);
        $tag->events()->detach($event); 
        return response(null, 204);
    }
}
