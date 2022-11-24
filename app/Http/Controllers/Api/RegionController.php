<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use App\Models\Departement;

class RegionController extends Controller
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
        $regions = Region::with('departements')->get();
        return response()->json(["success" => true, "message" => "Liste des régions", "data" => $regions]);
        
    }
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_region' => 'required', 'slug' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $region = Region::create($input);

        $array_departements = $request->departements;

        if(!empty($array_departements)){
            foreach($array_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $region->departements()->attach($departementObj);
            }
        }

        return response()->json(["success" => true, "message" => "Région créée avec succès.", "data" => $region]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $region = Region::find($id);
        if (is_null($region))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Région introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Région retrouvée avec succès.", "data" => $region]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Region $region)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_region' => 'required', 'slug' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $region->nom_region = $input['nom_region'];
        $region->slug = $input['slug'];
        //$region->status = $input['status'];
        $region->save();

        $array_departements = $request->departements;
        $old_departements = $region->departements();

        if(!empty($array_departements)){
            foreach($old_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $region->departements()->detach($departementObj);
            }
            foreach($array_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $region->departements()->attach($departementObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "Région modifiée avec succès.", "data" => $region]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Region $region)
    {
        $region->delete();
        return response()
            ->json(["success" => true, "message" => "Région supprimée avec succès.", "data" => $region]);
    }
}
