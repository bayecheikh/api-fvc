<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SourceFinancement;
use App\Models\Financement;

class SourceFinancementController extends Controller
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
 
        $source_financements = SourceFinancement::orderBy("libelle", "asc")->get();
        return response()->json(["success" => true, "message" => "Liste des source de financements", "data" => $source_financements]);

        
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
        $source_financement = SourceFinancement::create($input);

        return response()->json(["success" => true, "message" => "source de financement créée avec succès.", "data" => $source_financement]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $source_financement = SourceFinancement::find($id);
        if (is_null($source_financement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "source de financement introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "source de financement retrouvée avec succès.", "data" => $source_financement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SourceFinancement $source_financement)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $source_financement->libelle = $input['libelle'];

        $source_financement->save();
        return response()
            ->json(["success" => true, "message" => "source de financement modifiée avec succès.", "data" => $source_financement]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SourceFinancement $source_financement)
    {
        $source_financement->delete();
        return response()
            ->json(["success" => true, "message" => "source de financement supprimée avec succès.", "data" => $source_financement]);
    }
}
