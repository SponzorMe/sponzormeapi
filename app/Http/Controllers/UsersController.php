<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        //
    }
    /**
     * GET /users
     * @return array
     */
    public function index(){
        return User::all();
    }
    /**
     * GET /users/{1}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            return User::findOrFail($id);
        } catch (ModelNotFoundException $e){ 
            return response()->json([
                'error' => [
                    'message' => 'User not found'
                ]
            ], 404);
        }
    }
    /**
    * POST /users
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request) {
            $user = User::create($request->all());
            return response()->json(['created' => true], 201, [ 'Location' => route('users.show', ['id' => $user->id])]); 
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
        return $user;
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
