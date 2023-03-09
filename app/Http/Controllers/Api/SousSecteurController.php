<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Secteur;
use App\Models\SousSecteur;
use App\Models\Investissement;

class SousSecteurController extends Controller
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
 
        $sous_secteurs = SousSecteur::with('secteur')->get();
        return response()->json(["success" => true, "message" => "Liste des sous_secteurs", "data" => $sous_secteurs]);

        
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
        $sous_secteur = SousSecteur::create($input);

        return response()->json(["success" => true, "message" => "sous_secteur créée avec succès.", "data" => $sous_secteur]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sous_secteur = SousSecteur::with('secteur')->find($id);
        if (is_null($sous_secteur))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "sous_secteur introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "sous_secteur retrouvée avec succès.", "data" => $sous_secteur]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SousSecteur $sous_secteur)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $sous_secteur->libelle = $input['libelle'];
        $sous_secteur->save();
        return response()
            ->json(["success" => true, "message" => "sous_secteur modifiée avec succès.", "data" => $sous_secteur]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SousSecteur $sous_secteur)
    {
        $sous_secteur->delete();
        return response()
            ->json(["success" => true, "message" => "sous_secteur supprimée avec succès.", "data" => $sous_secteur]);
    }
}
