<?php

namespace App\Transformer;

use App\SponsorshipType;
use League\Fractal\TransformerAbstract;

/**
 * Class BundleTransformer
 * @package App\Transformer
 */
class SponsorshipTypeTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['event'];
    /**
     * Include a bundle's books
     * @param Bundle $bundle
     * @return \League\Fractal\Resource\Collection
     */
    public function includeEvent(SponsorshipType $sponsorshipType) 
    {
        return $this->item($sponsorshipType->event, new EventTransformer()); 
    }
    /**
     * Transform a Tag model into an array
     * 
     * @param Tag $tag
     * @return array
     */
     public function transform(SponsorshipType $sponsorshipType)
     {
         return [
            'id' => $sponsorshipType->id,
            'kind' => $sponsorshipType->kind,
            'cost' => $sponsorshipType->cost,
            'total_slots' => $sponsorshipType->total_slots,
            'available_slots' => $sponsorshipType->total_slots - $sponsorshipType->used_slots,
            'used_slots' => $sponsorshipType->used_slots,
            'created_at' => $sponsorshipType->created_at->toIso8601String(), 
            'updated_at' => $sponsorshipType->updated_at->toIso8601String()
         ];
     }
}