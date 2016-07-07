<?php

namespace App\Transformer;

use App\Task;
use League\Fractal\TransformerAbstract;

/**
 * Class BundleTransformer
 * @package App\Transformer
 */
class TaskTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['sponsorshipType'];
    /**
     * Include a bundle's books
     * @param Bundle $bundle
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSponsorship(Task $task) 
    {
        return $this->item($task->sponsorship, new SponsorshipTransformer()); 
    }
    /**
     * Transform a Tag model into an array
     * 
     * @param Tag $tag
     * @return array
     */
     public function transform(Task $task)
     {
         return [
            'id' => $task->id,
            'text' => $task->text,
            'owner_id' => $task->owner_id,
            'sponsorship_id' => $task->sponsorship_id,
            'status' => $task->status,
            'created_at' => $task->created_at->toIso8601String(), 
            'updated_at' => $task->updated_at->toIso8601String()
         ];
     }
}