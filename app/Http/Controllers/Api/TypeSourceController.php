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
        return response()->json(["success" => true, "message" => "Type source List", "data" => $typesources]);

        
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

        return response()->json(["success" => true, "message" => "Type source created successfully.", "data" => $type_source]);
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
            ->json(["success" => true, "message" => "Type source not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "Type source retrieved successfully.", "data" => $type_source]);
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
        $type_source->save();
        return response()
            ->json(["success" => true, "message" => "Type source updated successfully.", "data" => $type_source]);
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
            ->json(["success" => true, "message" => "Type source deleted successfully.", "data" => $type_source]);
    }
}
