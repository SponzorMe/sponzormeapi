<?php

namespace App\Http\Controllers;
use App\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Transformer\TaskTransformer;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
    * The validation rules
    *
    * @var array
    */
    protected $rules = [
            'text' => 'required|max:255',
            'owner_id' => 'required|exists:users,id',
            'sponsorship_id' => 'required|exists:sponsorships,id'
        ];

    /**
     * GET /task_types
     * @return array
     */
    public function index(){
        return $this->collection(Task::all(), new TaskTransformer());
    }

    /**
     * GET /task_types/{id}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(Task::findOrFail($id), new TaskTransformer());
    }

    /**
    * POST /task_types
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }    

        $task = Task::create($request->all());
        $data = $this->item($task, new TaskTransformer());
        return response()->json($data, 201, ['Location'=> route('tasks.show', ['id'=>$task->id])]);
    }
    /**
    * PUT /task_types/{id}
    *
    * @param Request $request
    * @param $id
    * @return mixed
    */
    public function update(Request $request, $id) {
        try{
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Task not found'
                ] 
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }   

        $task->fill($request->all());
        $task->save(); 
        return $this->item($task, new TaskTransformer());
    }
    /**
    * DELETE /task_types/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id) {
        try{
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Task not found'
                ] 
            ], 404);
        }
        $task->delete();
        return response(null, 204);
    }
}
