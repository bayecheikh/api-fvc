<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ObjectifTransversal;
use App\Models\Financement;

class ObjectifTransversalController extends Controller
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
 
        $objectif_transversals = ObjectifTransversal::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des objectif transversals", "data" => $objectif_transversals]);

        
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
        $objectif_transversal = ObjectifTransversal::create($input);

        return response()->json(["success" => true, "message" => "objectif transversal créée avec succès.", "data" => $objectif_transversal]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $objectif_transversal = ObjectifTransversal::find($id);
        if (is_null($objectif_transversal))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "objectif transversal introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "objectif transversal retrouvée avec succès.", "data" => $objectif_transversal]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObjectifTransversal $objectif_transversal)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $objectif_transversal->libelle = $input['libelle'];

        $objectif_transversal->save();
        return response()
            ->json(["success" => true, "message" => "objectif transversal modifiée avec succès.", "data" => $objectif_transversal]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectifTransversal $objectif_transversal)
    {
        $objectif_transversal->delete();
        return response()
            ->json(["success" => true, "message" => "objectif transversal supprimée avec succès.", "data" => $objectif_transversal]);
    }
}
