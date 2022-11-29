<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\DomaineFinancement;
use App\Models\Investissement;

class DomaineFinancementController extends Controller
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
 
        $domaine_financements = DomaineFinancement::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des domaine_financements", "data" => $domaine_financements]);

        
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
        $domaine_financement = DomaineFinancement::create($input);

        return response()->json(["success" => true, "message" => "domaine_financement créée avec succès.", "data" => $domaine_financement]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $domaine_financement = DomaineFinancement::find($id);
        if (is_null($domaine_financement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "domaine_financement introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "domaine_financement retrouvée avec succès.", "data" => $domaine_financement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DomaineFinancement $domaine_financement)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $domaine_financement->libelle = $input['libelle'];

        $domaine_financement->save();
        return response()
            ->json(["success" => true, "message" => "domaine_financement modifiée avec succès.", "data" => $domaine_financement]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomaineFinancement $domaine_financement)
    {
        $domaine_financement->delete();
        return response()
            ->json(["success" => true, "message" => "domaine_financement supprimée avec succès.", "data" => $domaine_financement]);
    }
}
