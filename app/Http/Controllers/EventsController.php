<?php

namespace App\Http\Controllers;
use App\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Transformer\EventTransformer;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
    * The validation rules
    *
    * @var array
    */
    protected $rules = [
            'title' => 'required|max:255',
            'summary' => 'required',
            'description' => 'required',
            'user_id' => 'required|exists:users,id',
            'type_id' => 'required|exists:types,id'
        ];

    /**
     * GET /events
     * @return array
     */
    public function index(){
        return $this->collection(Event::all(), new EventTransformer());
    }

    /**
     * GET /events/{id}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(Event::findOrFail($id), new EventTransformer());
    }

    /**
    * POST /events
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request) {

        

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }    

        $event = Event::create($request->all());
        $data = $this->item($event, new EventTransformer());
        return response()->json($data, 201, ['Location'=> route('events.show', ['id'=>$event->id])]);
    }
    /**
    * PUT /events/{id}
    *
    * @param Request $request
    * @param $id
    * @return mixed
    */
    public function update(Request $request, $id) {
        try{
            $event = Event::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Event not found'
                ] 
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }   

        $event->fill($request->all());
        $event->save(); 
        return $this->item($event, new EventTransformer());
    }
    /**
    * DELETE /events/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id) {
        try{
            $event = Event::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Event not found'
                ] 
            ], 404);
        }
        $event->delete();
        return response(null, 204);
    }
}
