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
    protected $defaultIncludes = ['sponsorshipType', 'tasks'];
    /**
     * Include a sponsorshipType
     * @param Sponsorship $sponsorship
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSponsorshipType(Sponsorship $sponsorship) 
    {
        return $this->item($sponsorship->sponsorshipType, new SponsorshipTypeTransformer()); 
    }
    /**
     * Include a Tasks
     * @param Sponsorship $sponsorship
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTasks(Sponsorship $sponsorship) 
    {
        return $this->collection($sponsorship->tasks, new TaskTransformer()); 
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
            'status' => $sponsorship->status,
            'created_at' => $sponsorship->created_at->toIso8601String(), 
            'updated_at' => $sponsorship->updated_at->toIso8601String()
         ];
     }
}