<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Structure;
use App\Models\Dimension;
use App\Models\TypeZoneIntervention;
use App\Models\TypeSource;
use App\Models\SourceFinancement;
use App\Models\Fichier;

class StructureController extends Controller
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
        $structures = Structure::with('users')
        ->with('regions')
        ->with('departements')
        ->with('dimensions')
        ->with('type_zone_interventions')
        ->with('type_sources')
        ->with('source_financements')
        ->with('fichiers')
        ->paginate(10);
        $total = $structures->total();
        return response()->json(["success" => true, "message" => "Structures List", "data" =>$structures,"total"=> $total]);
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function structureMultipleSearch($term)
    {
        $structures = Structure::where('id', 'like', '%'.$term.'%')->orWhere('nom_structure', 'like', '%'.$term.'%')
        ->with('users')
        ->with('regions')
        ->with('departements')
        ->with('dimensions')
        ->with('type_zone_interventions')
        ->with('type_sources')
        ->with('fichiers')
        ->with('source_financements')->paginate(10);
        $total = $structures->total();
        return response()->json(["success" => true, "message" => "Structures List", "data" =>$structures,"total"=> $total]);  
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
//a ajouter
        /* $nom_structure = $input['nom_structure'];
        $numero_autorisation = $input['numero_autorisation'];
        $accord_siege = $input['accord_siege'];
        $numero_agrement = $input['numero_agrement'];
        $adresse_structure = $input['adresse_structure'];
        $debut_intervention = $input['debut_intervention'];
        $fin_intervention = $input['fin_intervention'];
        $telephone_structure = $input['telephone_structure'];
        $email_structure = $input['email_structure'];

        $source_financements = explode (",", $input['source_financements']);
        $type_sources = explode (",", $input['type_sources']);
        $departements = explode (",", $input['departements']);
        $regions = explode (",", $input['regions']);
        $dimensions = explode (",", $input['dimensions']);
        $type_zone_interventions = explode (",", $input['type_zone_interventions']);

        $firstname_responsable = $input['firstname_responsable'];
        $lastname_responsable = $input['lastname_responsable'];
        $email_responsable = $input['email_responsable'];
        $telephone_responsable = $input['telephone_responsable'];
        $fonction_responsable = $input['fonction_responsable']; */

        $validator = Validator::make($input, ['nom_structure' => 'required','firstname_responsable' => 'required','lastname_responsable' => 'required', 'email_responsable' => 'required|unique:users,email']);
        if ($validator->fails())
        {
            return response()
            ->json($validator->errors());
        }
        else{
            $user = User::create([
                'name' => $input['firstname_responsable'].' '.$input['lastname_responsable'],
                'firstname' => $input['firstname_responsable'],
                'lastname' => $input['lastname_responsable'],
                'email' => $input['email_responsable'],
                'telephone' => $input['telephone_responsable'],
                'fonction' => $input['fonction_responsable'],
                'status' => $input['status'],
                'password' => bcrypt("@12345678")
            ]);
            $roleObj = Role::where('name','admin_structure')->first();
            $user->roles()->attach($roleObj);
    
            $structure = Structure::create(
                ['nom_structure' => $input['nom_structure'],
                'numero_autorisation' => $input['numero_autorisation'],
                'numero_agrement' => $input['numero_agrement'],
                'accord_siege' => '',
                'adresse_structure' => $input['adresse_structure'],
                'debut_intervention' => $input['debut_intervention'],
                'fin_intervention' => $input['fin_intervention'],
                'telephone_structure' => $input['telephone_structure'],
                'email_structure' => $input['email_structure'],
                'status' => $input['status']]
            );
    
            if ($request->hasFile('accord_siege') && $request->file('accord_siege')->isValid()) {
                $upload_path = public_path('upload');
                $file = $request->file('accord_siege');
                $file_name = $file->getClientOriginalName();
                $file_extension = $file->getClientOriginalExtension();
                $url_file = $upload_path . '/' . $file_name;
                $generated_new_name = 'accord_siege_' . time() . '.' . $file_extension;
                $file->move($upload_path, $generated_new_name);
    
                $fichierObj = Fichier::create([
                    'name' => $generated_new_name,
                    'url' => $url_file,
                    'extension' => $file_extension,
                    'description' => 'Accord de siège'
                ]);
                $structure->fichiers()->attach($fichierObj);
            }
            

            $array_source_financements = explode (",", $input['source_financements']);
            $array_type_sources = explode (",", $input['type_sources']);
            $array_departements = explode (",", $input['departements']);
            $array_regions = explode (",", $input['regions']);
            $array_dimensions = explode (",", $input['dimensions']);
            $array_type_zones = explode (",", $input['type_zone_interventions']);
    
            $structure->users()->attach($user);
    
            if(!empty($array_departements)){
                foreach($array_departements as $departement){
                    $departementObj = Departement::where('id',$departement)->first();
                    $structure->departements()->attach($departementObj);
                }
            }
    
            if(!empty($array_regions)){
                foreach($array_regions as $region){
                    $regionObj = Region::where('id',$region)->first();
                    $structure->regions()->attach($regionObj);
                }
            }
    
            if(!empty($array_dimensions)){
                foreach($array_dimensions as $dimension){
                    $dimensionObj = Dimension::where('id',$dimension)->first();
                    $structure->dimensions()->attach($dimensionObj);
                }
            }
    
            if(!empty($array_type_zones)){
                foreach($array_type_zones as $type_zone){
                    $type_zoneObj = TypeZoneIntervention::where('id',$type_zone)->first();
                    $structure->type_zone_interventions()->attach($type_zoneObj);
                }
            }
    
            if(!empty($array_type_sources)){
                foreach($array_type_sources as $type_source){
                    $type_sourceObj = TypeSource::where('id',$type_source)->first();
                    $structure->type_sources()->attach($type_sourceObj);
                }
            }
    
            if(!empty($array_source_financements)){
                foreach($array_source_financements as $source_financement){
                    $source_financementObj = SourceFinancement::where('id',$source_financement)->first();
                    $structure->source_financements()->attach($source_financementObj);
                }
            }
    
            return response()->json(["success" => true, "message" => "Structure created successfully.", "data" => $structure]);
            //return response()->json(["success" => true, "message" => "Structure created successfully.", "data" => $input]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $structure = Structure::with('regions')
        ->with('departements')
        ->with('dimensions')
        ->with('type_zone_interventions')
        ->with('type_sources')
        ->with('source_financements')
        ->with('fichiers')
        ->get()
        ->find($id);
        if (is_null($structure))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Structure not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "Structure retrieved successfully.", "data" => $structure]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Structure $structure)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_structure' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $structure->nom_structure = $input['nom_structure'];
        $structure->save();

        $array_departements = $request->departements;
        $array_regions = $request->regions;
        $array_dimensions = $request->dimensions;
        $array_type_zones = $request->type_zone_interventions;
        $array_source_financements = $request->source_financements;
        $array_type_sources = $request->type_sources;

        $old_departements = $structure->departements();
        $old_regions = $structure->regions();
        $old_dimensions = $structure->dimensions();
        $old_type_zones = $structure->type_zone_interventions();
        $old_source_financements = $structure->source_financements();
        $old_type_sources = $structure->type_sources();

        if(!empty($array_departements)){
            foreach($old_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $structure->departements()->detach($departementObj);
            }
            foreach($array_departements as $departement){
                $departementObj = Departement::where('id',$departement)->first();
                $structure->departements()->attach($departementObj);
            }
        }

        if(!empty($array_regions)){
            foreach($old_regions as $region){
                $regionObj = Region::where('id',$region)->first();
                $structure->regions()->detach($regionObj);
            }
            foreach($array_regions as $region){
                $regionObj = Region::where('id',$region)->first();
                $structure->regions()->attach($regionObj);
            }
        }

        if(!empty($array_dimensions)){
            foreach($old_dimensions as $dimension){
                $dimensionObj = Dimension::where('id',$dimension)->first();
                $structure->dimensions()->detach($dimensionObj);
            }
            foreach($array_dimensions as $dimension){
                $dimensionObj = Dimension::where('id',$dimension)->first();
                $structure->dimensions()->attach($dimensionObj);
            }
        }

        if(!empty($array_type_zones)){
            foreach($old_type_zones as $type_zone){
                $type_zoneObj = TypeZoneIntervention::where('id',$type_zone)->first();
                $structure->type_zone_interventions()->detach($type_zoneObj);
            }
            foreach($array_type_zones as $type_zone){
                $type_zoneObj = Dimension::where('id',$type_zone)->first();
                $structure->type_zone_interventions()->attach($type_zoneObj);
            }
        }

        if(!empty($array_type_sources)){
            foreach($old_type_sources as $type_source){
                $type_sourceObj = TypeSource::where('id',$type_source)->first();
                $structure->type_sources()->detach($type_sourceObj);
            }
            foreach($array_type_sources as $type_source){
                $type_sourceObj = TypeSource::where('id',$type_source)->first();
                $structure->type_sources()->attach($type_sourceObj);
            }
        }

        if(!empty($array_source_financements)){
            foreach($old_source_financements as $source_financement){
                $source_financementObj = SourceFinancement::where('id',$source_financement)->first();
                $structure->source_financements()->detach($source_financementObj);
            }
            foreach($array_source_financements as $source_financement){
                $source_financementObj = SourceFinancement::where('id',$source_financement)->first();
                $structure->source_financements()->attach($source_financementObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "structure updated successfully.", "data" => $structure]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Structure $structure)
    {
        $structure->delete();
        return response()
            ->json(["success" => true, "message" => "Structure deleted successfully.", "data" => $structure]);
    }
}
