<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Transformer\UserTransformer;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
    * The validation rules
    *
    * @var array
    */
    protected $rules = [
            'name' => 'required|max:255',
            'email' => 'required',
            'type' => 'required'
        ];

    /**
     * GET /users
     * @return array
     */
    public function index(){
        $users = User::with('ratings')->get();
        return $this->collection($users, new UserTransformer());
        
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

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }    

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

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
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
