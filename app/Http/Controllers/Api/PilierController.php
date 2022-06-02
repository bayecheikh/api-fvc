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

class PilierController extends Controller
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
 
        $piliers = Pilier::with('axes')->get();
        return response()->json(["success" => true, "message" => "pilier List", "data" => $piliers]);

        
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
        $validator = Validator::make($input, ['nom_pilier' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $pilier = Pilier::create($input);

        return response()->json(["success" => true, "message" => "pilier created successfully.", "data" => $pilier]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pilier = Pilier::find($id);
        if (is_null($pilier))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "pilier not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "pilier retrieved successfully.", "data" => $pilier]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pilier $pilier)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_pilier' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $pilier->nom_pilier = $input['nom_pilier'];
        $pilier->save();
        return response()
            ->json(["success" => true, "message" => "pilier updated successfully.", "data" => $pilier]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pilier $pilier)
    {
        $pilier->delete();
        return response()
            ->json(["success" => true, "message" => "pilier deleted successfully.", "data" => $pilier]);
    }
}
