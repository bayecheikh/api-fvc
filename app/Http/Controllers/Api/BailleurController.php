<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Bailleur;
use App\Models\Investissement;

class BailleurController extends Controller
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
 
        $bailleurs = Bailleur::with('investissements')->orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des bailleurs", "data" => $bailleurs]);

        
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
        $bailleur = Bailleur::create($input);

        return response()->json(["success" => true, "message" => "Bailleur créée avec succès.", "data" => $bailleur]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bailleur = bailleur::with('investissements')->find($id);
        if (is_null($bailleur))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Bailleur introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Bailleur retrouvée avec succès.", "data" => $bailleur]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, bailleur $bailleur)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $bailleur->libelle = $input['libelle'];

        $bailleur->save();
        return response()
            ->json(["success" => true, "message" => "Bailleur modifiée avec succès.", "data" => $bailleur]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(bailleur $bailleur)
    {
        $bailleur->delete();
        return response()
            ->json(["success" => true, "message" => "Bailleur supprimée avec succès.", "data" => $bailleur]);
    }
}
