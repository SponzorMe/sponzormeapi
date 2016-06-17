<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Transformer\RatingTransformer;

/**
 * Manage an User's Ratings
 */

class UsersRatingsController extends Controller {

    public function store(Request $request, $userId) {

        $user = User::findOrFail($userId);
        $rating = $user->ratings()->create(['value' => $request->get('value')]);
        $data = $this->item($rating, new RatingTransformer());
        return response()->json($data, 201); 
        
    }

    /**
    * @param $userId
    * @param $ratingId
    * @return \Laravel\Lumen\Http\ResponseFactory
    */
    public function destroy($userId, $ratingId) {
        /** @var \App\User $user */
        $user = User::findOrFail($userId);
        $user->ratings()->findOrFail($ratingId)->delete();
        return response(null, 204);
    }
    

}