<?php

namespace App\Http\Controllers;
use App\Sponsorship;
use App\SponsorshipType;
use App\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Transformer\SponsorshipTransformer;
use Illuminate\Http\Request;

class SponsorshipsController extends Controller
{
    /**
    * The validation rules
    *
    * @var array
    */
    protected $rules = [
            'cause' => 'required|max:255',
            'sponsor_id' => 'required|exists:users,id',
            'sponsorship_type_id' => 'required|exists:sponsorship_types,id'
        ];

    /**
     * GET /sponsorship_types
     * @return array
     */
    public function index(){
        return $this->collection(Sponsorship::all(), new SponsorshipTransformer());
    }

    /**
     * GET /sponsorship_types/{id}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(Sponsorship::findOrFail($id), new SponsorshipTransformer());
    }

    /**
    * POST /sponsorship_types
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }

        $sponsorship = Sponsorship::create($request->all());
        $data = $this->item($sponsorship, new SponsorshipTransformer());
        return response()->json($data, 201, ['Location'=> route('sponsorships.show', ['id'=>$sponsorship->id])]);
    }
    /**
    * PUT /sponsorship_types/{id}
    *
    * @param Request $request
    * @param $id
    * @return mixed
    */
    public function update(Request $request, $id) {
        try{
            $sponsorship = Sponsorship::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Sponsorship not found'
                ] 
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }   

        //If code is here we need perform 3 things:
        // 0. Verify we are changin pending to accepted
        // 1. We should verify theres an available slot in the sponsorship-Type
        // 1a. If not we return invalid Request
        // 1b. If yes we need get the perks associate to the sponsorship-Type
        // 2. We copy the perks in the tasks Model
        // 3. Decrement the sponsorships slots.

        //0a
        if($sponsorship->status == 'pending' && $request->input('status') == 'accepted'){
            //1
            $sponsorshipType = SponsorshipType::find($request->input('sponsorship_type_id'));
            if($sponsorshipType->total_slots <= $sponsorshipType->used_slots){
                return response()->json(['error'=> 'No Slots Available no status can be changed to accepted', 'message'=>'No updated'], 422);
            }
            else{
                //1b.
                $perks = SponsorshipType::find($request->input('sponsorship_type_id'))->perks;
                //2
                foreach ($perks as $p) {
                    $t = ['text'=>$p->title, 'owner_id'=>$request->input('sponsor_id'), 'sponsorship_id'=>$sponsorship->id, 'status'=>'pending', 'type'=>0];
                    Task::create($t);
                }
                //3
                $sponsorshipType->used_slots += 1;
                $sponsorshipType->save();
            }
        }//0b
        else if($sponsorship->status == 'accepted' && $request->input('status') == 'pending'){
            //1
            $sponsorshipType = SponsorshipType::find($request->input('sponsorship_type_id'));
            //1b.
            $tasks = Sponsorship::find($id)->tasks;
            //2
            foreach ($tasks as $t) {
                $t->delete();
            }
            //3
            $sponsorshipType->used_slots -= 1;
            $sponsorshipType->save();
        }
        $sponsorship->fill($request->all());
        $sponsorship->save();
        $sponsorship = Sponsorship::findOrFail($id);
        return $this->item($sponsorship, new SponsorshipTransformer());
    }
    /**
    * DELETE /sponsorship_types/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id) {
        try{
            $sponsorship = Sponsorship::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Sponsorship not found'
                ] 
            ], 404);
        }
        $sponsorship->delete();
        return response(null, 204);
    }
}
