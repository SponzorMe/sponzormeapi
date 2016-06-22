<?php

namespace App\Transformer;

use App\Type;
use League\Fractal\TransformerAbstract;

/**
 * Class BundleTransformer
 * @package App\Transformer
 */
class TypeTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['events'];
    /**
     * Include a bundle's books
     * @param Bundle $bundle
     * @return \League\Fractal\Resource\Collection
     */
    public function includeEvents(Type $type) 
    {
        return $this->collection($type->events, new EventTransformer()); 
    }
    /**
     * Transform a Tag model into an array
     * 
     * @param Tag $tag
     * @return array
     */
     public function transform(Type $type)
     {
         return [
            'id' => $type->id,
            'title' => $type->title,
            'description' => $type->description,
            'created_at' => $type->created_at->toIso8601String(), 
            'updated_at' => $type->updated_at->toIso8601String()
         ];
     }
}