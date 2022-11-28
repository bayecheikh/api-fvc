<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AgenceExecution;
use App\Models\Financement;

class AgenceExecutionController extends Controller
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
 
        $agence_executions = AgenceExecution::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des agence executions", "data" => $agence_executions]);

        
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
        $agence_execution = AgenceExecution::create($input);

        return response()->json(["success" => true, "message" => "agence execution créée avec succès.", "data" => $agence_execution]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agence_execution = AgenceExecution::with('financements')->find($id);
        if (is_null($agence_execution))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "agence execution introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "agence execution retrouvée avec succès.", "data" => $agence_execution]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgenceExecution $agence_execution)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $agence_execution->libelle = $input['libelle'];

        $agence_execution->save();
        return response()
            ->json(["success" => true, "message" => "agence execution modifiée avec succès.", "data" => $agence_execution]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgenceExecution $agence_execution)
    {
        $agence_execution->delete();
        return response()
            ->json(["success" => true, "message" => "agence execution supprimée avec succès.", "data" => $agence_execution]);
    }
}
