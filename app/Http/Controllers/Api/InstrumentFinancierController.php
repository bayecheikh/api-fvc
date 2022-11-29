<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\InstrumentFinancier;
use App\Models\Investissement;

class InstrumentFinancierController extends Controller
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
 
        $instrument_financiers = InstrumentFinancier::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des instrument_financiers", "data" => $instrument_financiers]);

        
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
        $instrument_financier = InstrumentFinancier::create($input);

        return response()->json(["success" => true, "message" => "instrument_financier créée avec succès.", "data" => $instrument_financier]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $instrument_financier = InstrumentFinancier::find($id);
        if (is_null($instrument_financier))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "instrument_financier introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "instrument_financier retrouvée avec succès.", "data" => $instrument_financier]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InstrumentFinancier $instrument_financier)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $instrument_financier->libelle = $input['libelle'];

        $instrument_financier->save();
        return response()
            ->json(["success" => true, "message" => "instrument_financier modifiée avec succès.", "data" => $instrument_financier]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(InstrumentFinancier $instrument_financier)
    {
        $instrument_financier->delete();
        return response()
            ->json(["success" => true, "message" => "instrument_financier supprimée avec succès.", "data" => $instrument_financier]);
    }
}
