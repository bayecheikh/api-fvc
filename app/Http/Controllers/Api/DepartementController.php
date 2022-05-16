<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use App\Models\Departement;

class DepartementController extends Controller
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
 
        $departements = Departement::with('region')->get();
        return response()->json(["success" => true, "message" => "Departement List", "data" => $departements]);

        
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
        $validator = Validator::make($input, ['nom_departement' => 'required', 'slug' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $departement = Departement::create($input);

        return response()->json(["success" => true, "message" => "Departement created successfully.", "data" => $departement]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $departement = departement::find($id);
        if (is_null($departement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "departement not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "departement retrieved successfully.", "data" => $departement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Departement $departement)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_departement' => 'required', 'slug' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $departement->nom_departement = $input['nom_departement'];
        $departement->slug = $input['slug'];
        $departement->save();
        return response()
            ->json(["success" => true, "message" => "departement updated successfully.", "data" => $departement]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Departement $departement)
    {
        $departement->delete();
        return response()
            ->json(["success" => true, "message" => "departement deleted successfully.", "data" => $departement]);
    }
}
