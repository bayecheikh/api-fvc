<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Pilier;
use App\Models\Axe;
use App\Models\Investissement;

class AxeController extends Controller
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
 
        $axes = Axe::with('pilier')->get();
        return response()->json(["success" => true, "message" => "Liste des axes", "data" => $axes]);

        
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
        $validator = Validator::make($input, ['nom_axe' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $axe = axe::create($input);

        return response()->json(["success" => true, "message" => "axe créée avec succès.", "data" => $axe]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $axe = Axe::with('pilier')->find($id);
        if (is_null($axe))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "axe introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "axe retrouvée avec succès.", "data" => $axe]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Axe $axe)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_axe' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $axe->nom_axe = $input['nom_axe'];
        $axe->save();
        return response()
            ->json(["success" => true, "message" => "axe modifiée avec succès.", "data" => $axe]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Axe $axe)
    {
        $axe->delete();
        return response()
            ->json(["success" => true, "message" => "axe supprimée avec succès.", "data" => $axe]);
    }
}
