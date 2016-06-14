<?php

namespace App\Transformer;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['events'];
    
    public function includeEvents(User $user)
    {
        return $this->collection($user->events, new EventTransformer());
    }
    /**
     * Transform a User model into an array
     * 
     * @param User $user
     * @return array
     */
     public function transform(User $user)
     {
         return [
             'id'           => $user->id,
             'name'         => $user->name,
             'email'        => $user->email,
             'type'         => $user->type,
             'created_at'   => $user->created_at->toIso8601String(),
             'updated_at'   => $user->updated_at->toIso8601String(),
             'released'     => $user->created_at->diffForHumans()
         ];
     }
}