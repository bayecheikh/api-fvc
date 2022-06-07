<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Monnaie;
use App\Models\Investissement;

class MonnaieController extends Controller
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
 
        $monnaies = Monnaie::with('investissements')->get();
        return response()->json(["success" => true, "message" => "Liste des monnaies", "data" => $monnaies]);

        
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
        $monnaie = Monnaie::create($input);

        return response()->json(["success" => true, "message" => "monnaie créée avec succès.", "data" => $monnaie]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $monnaie = Monnaie::with('investissements')->find($id);
        if (is_null($monnaie))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "monnaie introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "monnaie retrouvée avec succès.", "data" => $monnaie]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Monnaie $monnaie)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $monnaie->libelle = $input['libelle'];

        $monnaie->save();
        return response()
            ->json(["success" => true, "message" => "monnaie modifiée avec succès.", "data" => $monnaie]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Monnaie $monnaie)
    {
        $monnaie->delete();
        return response()
            ->json(["success" => true, "message" => "monnaie supprimée avec succès.", "data" => $monnaie]);
    }
}
