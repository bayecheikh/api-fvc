<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ObjectifAdaptation;
use App\Models\Financement;

class ObjectifAdaptationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
 
        $objectif_adaptations = ObjectifAdaptation::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des objectif adaptations", "data" => $objectif_adaptations]);

        
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $objectif_adaptation = ObjectifAdaptation::create($input);

        return response()->json(["success" => true, "message" => "objectif adaptation créée avec succès.", "data" => $objectif_adaptation]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $objectif_adaptation = ObjectifAdaptation::find($id);
        if (is_null($objectif_adaptation))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "objectif adaptation introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "objectif adaptation retrouvée avec succès.", "data" => $objectif_adaptation]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObjectifAdaptation $objectif_adaptation)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $objectif_adaptation->libelle = $input['libelle'];

        $objectif_adaptation->save();
        return response()
            ->json(["success" => true, "message" => "objectif adaptation modifiée avec succès.", "data" => $objectif_adaptation]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectifAdaptation $objectif_adaptation)
    {
        $objectif_adaptation->delete();
        return response()
            ->json(["success" => true, "message" => "objectif adaptation supprimée avec succès.", "data" => $objectif_adaptation]);
    }
}
