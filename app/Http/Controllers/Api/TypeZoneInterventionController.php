<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\type_zone;
use App\Models\Structure;
use App\Models\TypeZoneIntervention;

class TypeZoneInterventionController extends Controller
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
 
        $type_zones = TypeZoneIntervention::with('structures')->get();
        return response()->json(["success" => true, "message" => "Liste des types de zone d'intervention", "data" => $type_zones]);

        
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
        $validator = Validator::make($input, ['libelle_zone' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $type_zone = TypeZoneIntervention::create($input);

        return response()->json(["success" => true, "message" => "Type Zone Intervention créé avec succès.", "data" => $type_zone]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type_zone = TypeZoneIntervention::with('structures')->get()->find($id);
        if (is_null($type_zone))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "type zone intervention introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Type Zone Intervention retrouvé avec succès.", "data" => $type_zone]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeZoneIntervention $type_zone)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle_zone' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $type_zone->libelle_zone = $input['libelle_zone'];
        $type_zone->status = $input['status'];
        $type_zone->save();
        return response()
            ->json(["success" => true, "message" => "Type Zone Intervention modifié avec succès.", "data" => $type_zone]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeZoneIntervention $type_zone)
    {
        $type_zone->delete();
        return response()
            ->json(["success" => true, "message" => "Type Zone Intervention supprimé avec succès.", "data" => $type_zone]);
    }
}
