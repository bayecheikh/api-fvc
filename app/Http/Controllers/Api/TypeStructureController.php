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
use App\Models\TypeStructure;
use App\Models\Investissement;

class TypeStructureController extends Controller
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
 
        $type_structures = TypeStructure::with('secteur')->get();
        return response()->json(["success" => true, "message" => "Liste des type_structures", "data" => $type_structures]);

        
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
        $type_structure = TypeStructure::create($input);

        return response()->json(["success" => true, "message" => "type_structure créée avec succès.", "data" => $type_structure]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type_structure = TypeStructure::with('secteur')->find($id);
        if (is_null($type_structure))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "type_structure introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "type_structure retrouvée avec succès.", "data" => $type_structure]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeStructure $type_structure)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $type_structure->libelle = $input['libelle'];
        $type_structure->save();
        return response()
            ->json(["success" => true, "message" => "type_structure modifiée avec succès.", "data" => $type_structure]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SyousSecteur $type_structure)
    {
        $type_structure->delete();
        return response()
            ->json(["success" => true, "message" => "type_structure supprimée avec succès.", "data" => $type_structure]);
    }
}
