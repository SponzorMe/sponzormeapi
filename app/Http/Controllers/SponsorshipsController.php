<?php

namespace App\Http\Controllers;
use App\Sponsorship;
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

        $sponsorship->fill($request->all());
        $sponsorship->save(); 
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
