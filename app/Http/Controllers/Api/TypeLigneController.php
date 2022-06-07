<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\TypeLigne;

class TypeLigneController extends Controller
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
 
        $type_lignes = TypeLigne::with('ligne_financements')->get();
        return response()->json(["success" => true, "message" => "Liste des types de ligne", "data" => $type_lignes]);

        
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
        $type_ligne = TypeLigne::create($input);

        return response()->json(["success" => true, "message" => "type de ligne créé avec succès.", "data" => $type_ligne]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type_ligne = TypeLigne::with('ligne_financements')->find($id);
        if (is_null($type_ligne))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "type de ligne introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "type de ligne retrouvé avec succès.", "data" => $type_ligne]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeLigne $type_ligne)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $type_ligne->libelle = $input['libelle'];
        $type_ligne->save();
        return response()
            ->json(["success" => true, "message" => "type de ligne modifié avec succès.", "data" => $type_ligne]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeLigne $type_ligne)
    {
        $type_ligne->delete();
        return response()
            ->json(["success" => true, "message" => "type de ligne supprimé avec succès.", "data" => $type_ligne]);
    }
}
