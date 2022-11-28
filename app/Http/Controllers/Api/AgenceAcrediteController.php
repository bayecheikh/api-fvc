<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AgenceAcredite;
use App\Models\Financement;

class AgenceAcrediteController extends Controller
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
 
        $agence_acredites = AgenceAcredite::with('financements')->orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des agence acrédités", "data" => $agence_acredites]);

        
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
        $agence_acredite = AgenceAcredite::create($input);

        return response()->json(["success" => true, "message" => "agence acrédité créée avec succès.", "data" => $agence_acredite]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agence_acredite = AgenceAcredite::with('financements')->find($id);
        if (is_null($agence_acredite))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "agence acrédité introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "agence acrédité retrouvée avec succès.", "data" => $agence_acredite]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgenceAcredite $agence_acredite)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $agence_acredite->libelle = $input['libelle'];

        $agence_acredite->save();
        return response()
            ->json(["success" => true, "message" => "agence acrédité modifiée avec succès.", "data" => $agence_acredite]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgenceAcredite $agence_acredite)
    {
        $agence_acredite->delete();
        return response()
            ->json(["success" => true, "message" => "agence acrédité supprimée avec succès.", "data" => $agence_acredite]);
    }
}
