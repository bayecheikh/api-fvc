<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TypeZoneIntervention;
use App\Models\TypeSource;
use App\Models\SourceFinancement;
use Validator;

use App\Models\Role;
use App\Models\Permission;

use App\Models\Demande;

use App\Models\Investissement;
use App\Models\User;
use App\Models\Fichier;
use App\Models\Structure;
use App\Models\Annee;
use App\Models\Monnaie;
use App\Models\LigneFinancement;
use App\Models\ModeFinancement;
use App\Models\LigneModedemande;
use App\Models\Dimension;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Pilier;
use App\Models\Axe;

class DemandeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $demandes = Demande::with('profil')
            ->with('structure') 
            ->paginate(10);
        }
        else{
            if($request->user()->hasRole('directeur_eps')){
                $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
                $demandes = Demande::with('profil')
                ->with('structure') 
                ->whereHas('source', function($q) use ($source_id){
                    $q->where('id', $source_id);
                })->paginate(10);
            }
            else{
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $demandes = Demande::with('profil')
                ->with('structure') 
                ->whereHas('structure', function($q) use ($structure_id){
                    $q->where('id', $structure_id);
                })->orderBy('created_at', 'DESC')->paginate(10);
            }
            
        }

        
        $total = $demandes->total();
        return response()->json(["success" => true, "message" => "Demande List", "data" =>$demandes,"total"=> $total]);
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function demandeMultipleSearch($term)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $demandes = demande::where('id', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')
            ->with('profil')
            ->with('structure') 
            ->paginate(10);
        }else{
            $structure_id = User::find($request->user()->id)->structures[0]->id;
            $demandes = demande::where('id', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')
            ->with('profil')
            ->with('structure') 
            ->with('fichiers')->whereHas('structure', function($q) use ($structure_id){
                $q->where('id', $structure_id);
            })
            ->paginate(10);

            if($request->user()->hasRole('directeur_eps')){
                $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
                $demandes = demande::where('id', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')
                ->with('profil')
                ->with('structure') 
                ->whereHas('source', function($q) use ($source_id){
                    $q->where('id', $sourcee_id);
                })->paginate(10);
            }
            else{
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $demandes = demande::where('id', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')
                ->with('profil')
                ->with('structure') 
                ->whereHas('structure', function($q) use ($structure_id){
                    $q->where('id', $structure_id);
                })->paginate(10);
            }
        }
        $total = $demandes->total();
        return response()->json(["success" => true, "message" => "Liste des demandes", "data" =>$demandes,"total"=> $total]);  
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

        $structure_id = User::find($request->user()->id)->structures[0]->id;
        $source = User::find($request->user()->id)->structures[0]->source_financements[0];
        $source_id = $source->id;
        $source_libelle = $source->libelle_source;

        $validator = Validator::make($input, ['annee' => 'required','monnaie' => 'required']);
        if ($validator->fails())
        {
            return response()
            ->json($validator->errors());
        }
        else{ 
            if ($request->user()->hasRole('point_focal')){             
                $demande = demande::create(
                    ['state' => 'INITIER_demande']
                );
            }
            if ($request->user()->hasRole('admin_structure')){  
                $demande = demande::create(
                    ['state' => 'VALIDATION_ADMIN_STRUCTURE']
                );
            }  
            

            $annee = $input['annee'];
            $monnaie = $input['monnaie'];
            $region = $input['region'];
            $dimension = $input['dimension'];

            $libelleModeFinancements = explode (",", $input['libelleModeFinancements']);
            $montantModeFinancements = explode (",", $input['montantModeFinancements']);

            $piliers = explode (",", $input['piliers']); 
            $axes = explode (",", $input['axes']); 
            $montantBienServicePrevus = explode (",", $input['montantBienServicePrevus']);
            $montantBienServiceMobilises = explode (",", $input['montantBienServiceMobilises']);
            $montantBienServiceExecutes = explode (",", $input['montantBienServiceExecutes']);
            $montantdemandePrevus = explode (",", $input['montantdemandePrevus']);
            $montantdemandeMobilises = explode (",", $input['montantdemandeMobilises']);
            $montantdemandeExecutes = explode (",", $input['montantdemandeExecutes']);

            $tempLigneModeFinancements = str_replace("\\", "",$input['ligneModeFinancements']);
            $ligneModeFinancements = json_decode($tempLigneModeFinancements);
 
            $tempLigneFinancements = str_replace("\\", "",$input['ligneModeFinancements']);
            $ligneFinancements = json_decode($tempLigneFinancements);

            $fichiers = $input['fichiers'];

            if($structure_id!=null){               
                $structureObj = Structure::where('id',intval($structure_id))->first();
                $demande->structure()->attach($structureObj);
            }
            if($source_id!=null){               
                $sourceObj = SourceFinancement::where('id',intval($source_id))->first();
                $demande->source()->attach($sourceObj);
            }

            if($annee!=null){               
                $anneeObj = Annee::where('id',$annee)->first();
                $demande->annee()->attach($anneeObj);
            }
            if($monnaie!=null){               
                $monnaieObj = Monnaie::where('id',$monnaie)->first();
                $demande->monnaie()->attach($monnaieObj);
            }
            if($region!=null){               
                $regionObj = Region::where('id',$region)->first();
                $demande->region()->attach($regionObj);
            }
            if($dimension!=null){               
                $dimensionObj = Dimension::where('id',$dimension)->first();
                $demande->dimension()->attach($dimensionObj);
            }
            $imode=0;
            if(!empty($libelleModeFinancements)){
                foreach($libelleModeFinancements as $libelleModeFinancement){
                    $ligneModeFinancementObj = ModeFinancement::create([
                        'libelle' => $libelleModeFinancement,
                        'montant' => $montantModeFinancements[$imode],
                        'status' => 'actif'
                    ]);
                    $demande->mode_financements()->attach($ligneModeFinancementObj);
                    $imode++;
                }
            }
            $ifinance=0;
            if(!empty($piliers)){
                foreach($piliers as $pilier){
                    $pilierObj = Pilier::where('id',intval($pilier))->first();
                    $demande_id = $demande->id;

                    $demande->piliers()->detach($pilierObj);
                    $demande->piliers()->attach($pilierObj);

                    $axeObj = Axe::where('id',intval($axes[$ifinance]))->first();

                    $demande->axes()->detach($axeObj);
                    $demande->axes()->attach($axeObj);

                    $ligneFinancementObj = LigneFinancement::create([                      
                        'id_pilier'=> intval($pilier), 
                        'id_axe'=> intval($axes[$ifinance]), 
                        'montantBienServicePrevus'=> $montantBienServicePrevus[$ifinance],
                        'montantBienServiceMobilises'=> $montantBienServiceMobilises[$ifinance],
                        'montantBienServiceExecutes'=> $montantBienServiceExecutes[$ifinance],
                        'montantdemandePrevus'=> $montantdemandePrevus[$ifinance],
                        'montantdemandeMobilises'=> $montantdemandeMobilises[$ifinance],
                        'montantdemandeExecutes'=> $montantdemandeExecutes[$ifinance], 
                        'status' => 'actif'
                    ]);
                    $demande->ligne_financements()->attach($ligneFinancementObj);
                    $ifinance++;
                }
            }

            /* $user = User::create([
                'name' => $input['firstname_responsable'].' '.$input['lastname_responsable'],
                'firstname' => $input['firstname_responsable'],
                'lastname' => $input['lastname_responsable'],
                'email' => $input['email_responsable'],
                'telephone' => $input['telephone_responsable'],
                'fonction' => $input['fonction_responsable'],
                'status' => 'actif',
                'password' => bcrypt("@12345678")
            ]);
            $structure->users()->attach($user);
    
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
             */

            /* $array_source_financements = explode (",", $input['source_financements']);
            $array_type_sources = explode (",", $input['type_sources']);
            $array_departements = explode (",", $input['departements']);
            $array_regions = explode (",", $input['regions']);
            $array_dimensions = explode (",", $input['dimensions']);
            $array_type_zones = explode (",", $input['type_zone_interventions']);
    
            
    
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
            } */
    
            return response()->json(["success" => true, "message" => "demande enregistré avec succès.", "data" => $source_libelle]);
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
        $demande = demande::with('region')
        ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('source')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')
        ->get()
        ->find($id);
        if (is_null($demande))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "demande not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "demande retrieved successfully.", "data" => $demande]);
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
        else{
        $structure->nom_structure = $input['nom_structure'];
        $structure->numero_autorisation = $input['numero_autorisation'];
        $structure->numero_agrement = $input['numero_agrement'];
        $structure->accord_siege = '';
        $structure->adresse_structure = $input['adresse_structure'];
        $structure->debut_intervention = $input['debut_intervention'];
        $structure->fin_intervention = $input['fin_intervention'];
        $structure->telephone_structure = $input['telephone_structure'];
        $structure->email_structure = $input['email_structure'];
        $structure->status = 'actif';
        $structure->save();

        $array_source_financements = explode (",", $input['source_financements']);
        $array_type_sources = explode (",", $input['type_sources']);
        $array_departements = explode (",", $input['departements']);
        $array_regions = explode (",", $input['regions']);
        $array_dimensions = explode (",", $input['dimensions']);
        $array_type_zones = explode (",", $input['type_zone_interventions']);

        $old_departements = $structure->departements();
        $old_regions = $structure->regions();
        $old_dimensions = $structure->dimensions();
        $old_type_zones = $structure->type_zone_interventions();
        $old_source_financements = $structure->source_financements();
        $old_type_sources = $structure->type_sources();
        $old_fichiers = $structure->fichiers();

        if ($request->hasFile('accord_siege') && $request->file('accord_siege')->isValid()) {

            foreach($old_fichiers as $fichier){
                $fichierObj = Fichier::where('id',$fichier)->first();
                $structure->fichiers()->detach($fichierObj);
            }
            
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
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(demande $demande)
    {
        $demande->delete();
        return response()
            ->json(["success" => true, "message" => "demande supprimé.", "data" => $demande]);
    }


    /////////////////////////////////////////   WORKFLOW / ///////////////////////////
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validation_demande(Request $request)
    {
        $input = $request->all();
        

        $demande = demande::where('id',$input['id'])->first();

        if ($request->user()->hasRole('point_focal')){
            $demande->state = 'VALIDATION_ADMIN_STRUCTURE';
            $demande->status = 'a_valider';
        }
        if ($request->user()->hasRole('admin_structure')){

            if($demande->source[0]->libelle_source=='EPS'){
                $demande->state = 'VALIDATION_DIRECTEUR_EPS';
                $demande->status = 'a_valider';
            }
            else{
                $demande->state = 'FIN_PROCESS';
                $demande->status = 'publie';
            }
        }
        if ($request->user()->hasRole('directeur_eps')){
            $demande->state = 'FIN_PROCESS';
            $demande->status = 'publie';
        }
        $demande->save();

        return response()->json(["success" => true, "message" => "demande validé", "data" =>$demande]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rejet_demande(Request $request)
    {
        $input = $request->all();
        $motif_rejet = $input['motif_rejet'];
        

        $demande = demande::where('id',$input['id'])->first();

        if ($request->user()->hasRole('admin_structure')){          
            $demande->state = 'INITIER_demande';
            $demande->status = 'rejete';          
            $demande->motif_rejet = $motif_rejet;          
        }
        if ($request->user()->hasRole('directeur_eps')){
            $demande->state = 'VALIDATION_ADMIN_STRUCTURE';
            $demande->status = 'rejete';
            $demande->motif_rejet = $motif_rejet; 
        }
        if ($request->user()->hasRole('admin_dprs')){
            $demande->state = 'VALIDATION_ADMIN_STRUCTURE';
            $demande->status = 'rejete';
            $demande->motif_rejet = $motif_rejet; 
        }
        $demande->save();

        return response()->json(["success" => true, "message" => "demande rejeté avec succés", "data" =>$demande]);  
    }
}
