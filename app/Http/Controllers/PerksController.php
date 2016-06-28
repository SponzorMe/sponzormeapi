<?php

namespace App\Http\Controllers;
use App\Perk;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Transformer\PerkTransformer;
use Illuminate\Http\Request;

class PerksController extends Controller
{
    /**
    * The validation rules
    *
    * @var array
    */
    protected $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'sponsorship_type_id' => 'required|exists:sponsorship_types,id'
        ];

    /**
     * GET /sponsorship_types
     * @return array
     */
    public function index(){
        return $this->collection(Perk::all(), new PerkTransformer());
    }

    /**
     * GET /sponsorship_types/{id}
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(Perk::findOrFail($id), new PerkTransformer());
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

        $perk = Perk::create($request->all());
        $data = $this->item($perk, new PerkTransformer());
        return response()->json($data, 201, ['Location'=> route('perks.show', ['id'=>$perk->id])]);
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
            $perk = Perk::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Perk not found'
                ] 
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }   

        $perk->fill($request->all());
        $perk->save(); 
        return $this->item($perk, new PerkTransformer());
    }
    /**
    * DELETE /sponsorship_types/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id) {
        try{
            $perk = Perk::findOrFail($id);
        } catch (ModelNotFoundException $e) { 
            return response()->json([
                'error' => [
                    'message' => 'Perk not found'
                ] 
            ], 404);
        }
        $perk->delete();
        return response(null, 204);
    }
}
