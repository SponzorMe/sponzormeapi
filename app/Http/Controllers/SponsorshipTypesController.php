<?php

namespace App\Http\Controllers;
use App\SponsorshipType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Transformer\SponsorshipTypeTransformer;
use Illuminate\Http\Request;

class SponsorshipTypesController extends Controller
{
    /**
    * The validation rules
    *
    * @var array
    */
    protected $rules = [
            'kind' => 'required|max:255',
            'cost' => 'required',
            'total_slots' => 'required',
            'event_id' => 'required|exists:events,id'
        ];

    /**
     * GET /sponsorship_types
     * @return array
     */
    public function index(){
        return $this->collection(SponsorshipType::all(), new SponsorshipTypeTransformer());
    }

    /**
     * GET /sponsorship_types/{id}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(SponsorshipType::findOrFail($id), new SponsorshipTypeTransformer());
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

        $sponsorshipType = SponsorshipType::create($request->all());
        $data = $this->item($sponsorshipType, new SponsorshipTypeTransformer());
        return response()->json($data, 201, ['Location'=> route('sponsorship_types.show', ['id'=>$sponsorshipType->id])]);
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
            $sponsorshipType = SponsorshipType::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'SponsorshipType not found'
                ] 
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }   

        $sponsorshipType->fill($request->all());
        $sponsorshipType->save(); 
        return $this->item($sponsorshipType, new SponsorshipTypeTransformer());
    }
    /**
    * DELETE /sponsorship_types/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id) {
        try{
            $sponsorshipType = SponsorshipType::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'SponsorshipType not found'
                ] 
            ], 404);
        }
        $sponsorshipType->delete();
        return response(null, 204);
    }
}
