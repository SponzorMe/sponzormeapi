<?php

namespace App\Transformer;

use App\Sponsorship;
use League\Fractal\TransformerAbstract;

/**
 * Class BundleTransformer
 * @package App\Transformer
 */
class SponsorshipTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['sponsorshipType'];
    /**
     * Include a bundle's books
     * @param Bundle $bundle
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSponsorshipType(Sponsorship $sponsorship) 
    {
        return $this->item($sponsorship->sponsorshipType, new SponsorshipTypeTransformer()); 
    }
    /**
     * Transform a Tag model into an array
     * 
     * @param Tag $tag
     * @return array
     */
     public function transform(Sponsorship $sponsorship)
     {
         return [
            'id' => $sponsorship->id,
            'cause' => $sponsorship->cause,
            'sponsor_id' => $sponsorship->sponsor_id,
            'sponsorship_type_id' => $sponsorship->sponsorship_type_id,
            'created_at' => $sponsorship->created_at->toIso8601String(), 
            'updated_at' => $sponsorship->updated_at->toIso8601String()
         ];
     }
}