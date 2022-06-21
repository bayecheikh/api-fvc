<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LigneModeInvestissement;
use App\Models\Dimension;
use App\Models\Investissement;
use Validator;

class LigneModeInvestissementController extends Controller
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
 
        $ligne_mode_investissements = LigneModeInvestissement::with('dimension')->get();
        return response()->json(["success" => true, "message" => "Liste ligne mode d'investissement ", "data" => $ligne_mode_investissements]);

        
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
        $ligne_mode_investissement = LigneModeInvestissement::create([
            'libelle' => $input['libelle'],
            'slug' => $input['slug'],
            'predefini' => $input['predefini'],
            'status' => 'actif'
        ]);

        return response()->json(["success" => true, "message" => "Ligne mode d'investissement créée avec succès.", "data" => $ligne_mode_investissement]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ligne_mode_investissement = LigneModeInvestissement::with('dimension')->find($id);
        if (is_null($ligne_mode_investissement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Ligne mode d'investissement introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Ligne mode d'investissement retrouvée avec succès.", "data" => $ligne_mode_investissement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LigneModeInvestissement $ligne_mode_investissement)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $ligne_mode_investissement->libelle = $input['libelle'];
        $ligne_mode_investissement->slug = $input['slug'];
        $ligne_mode_investissement->predefini = $input['predefini'];

        $ligne_mode_investissement->save();
        return response()
            ->json(["success" => true, "message" => "Ligne mode d'investissement modifiée avec succès.", "data" => $ligne_mode_investissement]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(LigneModeInvestissement $ligne_mode_investissement)
    {
        $ligne_mode_investissement->delete();
        return response()
            ->json(["success" => true, "message" => "Ligne mode d'investissement supprimée avec succès.", "data" => $ligne_mode_investissement]);
    }
}