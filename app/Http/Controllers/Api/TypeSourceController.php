<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\TypeSource;
use App\Models\SourceFinancement;
use App\Models\Structure;

class TypeSourceController extends Controller
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
 
        $typesources = TypeSource::with('structures')->with('sources')->get();
        return response()->json(["success" => true, "message" => "Liste des types source", "data" => $typesources]);

        
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
        $validator = Validator::make($input, ['libelle_type_source' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $type_source = TypeSource::create($input);

        return response()->json(["success" => true, "message" => "Type source créé avec succès.", "data" => $type_source]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type_source = TypeSource::with('structures')->with('sources')->get()->find($id);
        if (is_null($type_source))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Type source introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Type source retrouvé avec succès.", "data" => $type_source]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeSource $type_source)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle_type_source' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $type_source->libelle_type_source = $input['libelle_type_source'];
        $type_source->status = $input['status'];
        $type_source->save();
        return response()
            ->json(["success" => true, "message" => "Type source modifié avec succès.", "data" => $type_source]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeSource $type_source)
    {
        $type_source->delete();
        return response()
            ->json(["success" => true, "message" => "Type source supprimé avec succès.", "data" => $type_source]);
    }
}
