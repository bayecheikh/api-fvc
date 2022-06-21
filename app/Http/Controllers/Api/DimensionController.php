<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Dimension;
use App\Models\Structure;
use App\Models\LigneModeInvestissement;

class DimensionController extends Controller
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
 
        $dimensions = Dimension::with('structures')->with('ligne_modes')->get();
        return response()->json(["success" => true, "message" => "Liste des dimensions ", "data" => $dimensions]);

        
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
        $validator = Validator::make($input, ['libelle_dimension' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $dimension = Dimension::create($input);

        $array_ligne_modes = $request->ligne_modes;

        if(!empty($array_ligne_modes)){
            foreach($array_ligne_modes as $ligne_mode){
                $ligne_modeObj = LigneModeInvestissement::where('id',$ligne_mode)->first();
                $dimension->ligne_modes()->attach($ligne_modeObj);
            }
        }

        return response()->json(["success" => true, "message" => "Dimension créée avec succès.", "data" => $dimension]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dimension = Dimension::with('structures')->with('ligne_modes')->get()->find($id);
        if (is_null($dimension))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Dimension introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Dimension retrouvée avec succès.", "data" => $dimension]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dimension $dimension)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle_dimension' => '']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }

        $array_ligne_modes = $request->ligne_modes;
        $old_ligne_modes = $dimension->ligne_modes();

        if(!empty($array_ligne_modes)){
            foreach($old_ligne_modes as $ligne_mode){
                $ligne_modeObj = LigneModeInvestissement::where('id',$ligne_mode)->first();
                $dimension->ligne_modes()->detach($ligne_modeObj);
            }
            foreach($array_ligne_modes as $ligne_mode){
                $ligne_modeObj = LigneModeInvestissement::where('id',$ligne_mode)->first();
                $dimension->ligne_modes()->attach($ligne_modeObj);
            }
        }

        $dimension->libelle_dimension = $input['libelle_dimension'];
        $dimension->save();

        return response()
            ->json(["success" => true, "message" => "Dimension modifiée avec succès.", "data" => $dimension]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dimension $dimension)
    {
        $dimension->delete();
        return response()
            ->json(["success" => true, "message" => "Dimension supprimée avec succès.", "data" => $dimension]);
    }
}
