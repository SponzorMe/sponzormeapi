<?php

namespace App\Transformer;

use App\Perk;
use League\Fractal\TransformerAbstract;

/**
 * Class BundleTransformer
 * @package App\Transformer
 */
class PerkTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['sponsorshipType'];
    /**
     * Include a bundle's books
     * @param Bundle $bundle
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSponsorshipType(Perk $perk) 
    {
        return $this->item($perk->sponsorshipType, new SponsorshipTypeTransformer()); 
    }
    /**
     * Transform a Tag model into an array
     * 
     * @param Tag $tag
     * @return array
     */
     public function transform(Perk $perk)
     {
         return [
            'id' => $perk->id,
            'title' => $perk->title,
            'description' => $perk->description,
            'created_at' => $perk->created_at->toIso8601String(), 
            'updated_at' => $perk->updated_at->toIso8601String()
         ];
     }
}