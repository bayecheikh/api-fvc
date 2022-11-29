<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ObjectifAttenuation;
use App\Models\Financement;

class ObjectifAttenuationController extends Controller
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
 
        $objectif_attenuations = ObjectifAttenuation::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des objectif attenuations", "data" => $objectif_attenuations]);

        
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
        $objectif_attenuation = ObjectifAttenuation::create($input);

        return response()->json(["success" => true, "message" => "objectif attenuation créée avec succès.", "data" => $objectif_attenuation]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $objectif_attenuation = ObjectifAttenuation::find($id);
        if (is_null($objectif_attenuation))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "objectif attenuation introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "objectif attenuation retrouvée avec succès.", "data" => $objectif_attenuation]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObjectifAttenuation $objectif_attenuation)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $objectif_attenuation->libelle = $input['libelle'];

        $objectif_attenuation->save();
        return response()
            ->json(["success" => true, "message" => "objectif attenuation modifiée avec succès.", "data" => $objectif_attenuation]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectifAttenuation $objectif_attenuation)
    {
        $objectif_attenuation->delete();
        return response()
            ->json(["success" => true, "message" => "objectif attenuation supprimée avec succès.", "data" => $objectif_attenuation]);
    }
}
