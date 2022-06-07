<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\TypeSource;
use App\Models\SourceFinancement;
use App\Models\Structure;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use App\Models\Departement;

use App\Models\Dimension;
use App\Models\TypeZoneIntervention;

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
        $source_inancement = SourceFinancement::with('type_sources')->with('structures')->get();
        return response()->json(["success" => true, "message" => "Liste des sources de financement", "data" => $source_inancement]);
        
    }
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle_source' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $source = SourceFinancement::create($input);

        $array_type_sources = $request->type_sources;

        if(!empty($array_type_sources)){
            foreach($array_type_sources as $type_source){
                $type_sourceObj = TypeSource::where('id',$type_source)->first();
                $source->type_sources()->attach($type_sourceObj);
            }
        }

        return response()->json(["success" => true, "message" => "Source de financement créée avec succès.", "data" => $source]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $source_financement = SourceFinancement::with('type_sources')->with('structures')->get()->find($id);
        if (is_null($source_financement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Source de financement introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Source de financement retrouvée avec succès.", "data" => $source_financement]);
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
        $validator = Validator::make($input, ['libelle_source' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $source_financement->libelle_source = $input['libelle_source'];
        $source_financement->status = $input['status'];
        $source_financement->save();

        $array_type_sources = $request->type_sources;
        
        $old_type_sources = $source_financement->type_sources();
        
        if(!empty($array_type_sources)){
            foreach($old_type_sources as $type_source){
                $type_sourceObj = TypeSource::where('id',$type_source)->first();
                $source_financement->type_sources()->detach($type_sourceObj);
            }
            foreach($array_type_sources as $type_source){
                $type_sourceObj = TypeSource::where('id',$type_source)->first();
                $source_financement->type_sources()->attach($type_sourceObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "Source de financement modifiée avec succès.", "data" =>  $source_financement]);
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
            ->json(["success" => true, "message" => "Source de financement supprimée avec succès.", "data" => $source_financement]);
    }
}
