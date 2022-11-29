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

class SecteurController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $secteurs = Secteur::with('sous_secteurs')->get();
        return response()->json(["success" => true, "message" => "Liste des secteurs", "data" => $secteurs]);
        
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
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $secteur = Secteur::create($input);

        $array_sous_secteurs = $request->sous_secteurs;

        if(!empty($array_sous_secteurs)){
            foreach($array_sous_secteurs as $sous_secteur){
                $sous_secteurObj = SousSecteur::where('id',$sous_secteur)->first();
                $secteur->sous_secteurs()->attach($sous_secteurObj);
            }
        }

        return response()->json(["success" => true, "message" => "secteur créé avec succès.", "data" => $secteur]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $secteur = Secteur::with('sous_secteurs')->find($id);
        if (is_null($secteur))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "secteur introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "secteur retrouvé avec succès.", "data" => $secteur]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Secteur $secteur)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $secteur->libelle = $input['libelle'];
        $secteur->save();

        $array_sous_secteurs = $request->sous_secteurs;
        $old_sous_secteurs = $secteur->sous_secteurs();

        if(!empty($array_sous_secteurs)){
            foreach($old_sous_secteurs as $sous_secteur){
                $sous_secteurObj = SousSecteur::where('id',$sous_secteur)->first();
                $secteur->sous_secteurs()->detach($sous_secteurObj);
            }
            foreach($array_sous_secteurs as $sous_secteur){
                $sous_secteurObj = SousSecteur::where('id',$sous_secteur)->first();
                $secteur->sous_secteurs()->attach($sous_secteurObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "secteur modifié avec succès.", "data" => $secteur]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Secteur $secteur)
    {
        $secteur->delete();
        return response()
            ->json(["success" => true, "message" => "secteur supprimé avec succès.", "data" => $secteur]);
    }
}
