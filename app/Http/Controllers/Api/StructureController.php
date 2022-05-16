<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Structure;

class StructureController extends Controller
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
        $structures = Structure::with('regions')->with('departements')->get();
        return response()->json(["success" => true, "message" => "Structure List", "data" => $structures]);
        
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
        $validator = Validator::make($input, ['nom_structure' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $structure = Structure::create($input);

        $array_departements = $request->departements;
        $array_regions = $request->regions;

        if(!empty($array_departements)){
            foreach($array_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $structure->departements()->attach($departementObj);
            }
        }

        if(!empty($array_regions)){
            foreach($array_regions as $region){
                $regionObj = Departement::where('id',$region)->first();
                $structure->regions()->attach($regionObj);
            }
        }

        return response()->json(["success" => true, "message" => "Structure created successfully.", "data" => $structure]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $structure = Structure::with('regions')->with('departements')->get()->find($id);
        if (is_null($structure))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Structure not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "Structure retrieved successfully.", "data" => $structure]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Structure $structure)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_structure' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $structure->nom_structure = $input['nom_structure'];
        $structure->save();

        $array_departements = $request->departements;
        $array_regions = $request->regions;
        $old_departements = $structure->departements();
        $old_regions = $structure->regions();

        if(!empty($array_departements)){
            foreach($old_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $structure->departements()->detach($departementObj);
            }
            foreach($array_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $structure->departements()->attach($departementObj);
            }
        }

        if(!empty($array_regions)){
            foreach($old_regions as $region){
                $regionObj = Region::where('id',$region)->first();
                $structure->regions()->detach($regionObj);
            }
            foreach($array_regions as $region){
                $regionObj = Region::where('id',$region)->first();
                $structure->regions()->attach($regionObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "structure updated successfully.", "data" => $structure]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Structure $structure)
    {
        $structure->delete();
        return response()
            ->json(["success" => true, "message" => "Structure deleted successfully.", "data" => $structure]);
    }
}
