<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Transformer\UserTransformer;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * GET /users
     * @return array
     */
    public function index(){
        return $this->collection(User::all(), new UserTransformer());
        
    }
    /**
     * GET /users/{id}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(User::findOrFail($id), new UserTransformer());
    }
    /**
    * POST /users
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request) {
            $user = User::create($request->all());
            $data = $this->item($user, new UserTransformer());
            return response()->json($data, 201, ['Location'=> route('users.show', ['id'=>$user->id])]);
    }
    /**
    * PUT /users/{id}
    *
    * @param Request $request
    * @param $id
    * @return mixed
    */
    public function update(Request $request, $id) {
        try{
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'User not found'
                ] 
            ], 404);
        }
        $user->fill($request->all());
        $user->save(); 
        return $this->item($user, new UserTransformer());
    }
    /**
    * DELETE /users/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id) {
        try{
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'User not found'
                ] 
            ], 404);
        }
        $user->delete();
        return response(null, 204);
    }
}
