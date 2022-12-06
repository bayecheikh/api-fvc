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

use App\Models\Investissement;
use App\Models\User;
use App\Models\Fichier;
use App\Models\Structure;
use App\Models\Annee;
use App\Models\Monnaie;
use App\Models\LigneFinancement;
use App\Models\ModeFinancement;
use App\Models\LigneModeFinancement;
use App\Models\Dimension;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Bailleur;
use App\Models\Pilier;
use App\Models\Axe;

class InvestissementController extends Controller
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
    public function index(Request $request)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('source')
            ->with('dimension')
            ->with('piliers')->with('bailleurs')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')
            ->paginate(10);
        }
        else{
            if($request->user()->hasRole('directeur_eps')){
                $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
                $investissements = Investissement::with('region')
                ->with('annee')
                ->with('monnaie')
                ->with('structure')
                ->with('source')
                ->with('dimension')
                ->with('piliers')->with('bailleurs')
                ->with('axes')
                ->with('mode_financements')
                ->with('ligne_financements')
                ->with('fichiers')
                ->whereHas('source', function($q) use ($source_id){
                    $q->where('id', $source_id);
                })->paginate(10);
            }
            else{
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $investissements = Investissement::with('region')
                ->with('annee')
                ->with('monnaie')
                ->with('structure')
                ->with('source')
                ->with('dimension')
                ->with('piliers')->with('bailleurs')
                ->with('axes')
                ->with('mode_financements')
                ->with('ligne_financements')
                ->with('fichiers')
                ->whereHas('structure', function($q) use ($structure_id){
                    $q->where('id', $structure_id);
                })->orderBy('created_at', 'DESC')->paginate(10);
                $investissements->load('axes.ligne_financements');
            }
            
        }

        
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Structures List", "data" =>$investissements,"total"=> $total]);
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function investissementMultipleSearch($term)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $investissements = Investissement::where('id', 'like', '%'.$term.'%')->orWhere('nom_investissement', 'like', '%'.$term.'%')
            ->with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('source')
            ->with('dimension')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')
            ->paginate(10);
        }else{
            $structure_id = User::find($request->user()->id)->structures[0]->id;
            $investissements = Investissement::where('id', 'like', '%'.$term.'%')->orWhere('nom_investissement', 'like', '%'.$term.'%')
            ->with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('source')
            ->with('dimension')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')->whereHas('structure', function($q) use ($structure_id){
                $q->where('id', $structure_id);
            })
            ->paginate(10);

            if($request->user()->hasRole('directeur_eps')){
                $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
                $investissements = Investissement::where('id', 'like', '%'.$term.'%')->orWhere('nom_investissement', 'like', '%'.$term.'%')
                ->with('annee')
                ->with('region')
                ->with('monnaie')
                ->with('structure')
                ->with('source')
                ->with('dimension')
                ->with('bailleurs')
                ->with('piliers')->with('bailleurs')
                ->with('axes')
                ->with('mode_financements')
                ->with('ligne_financements')
                ->with('fichiers')
                ->whereHas('source', function($q) use ($source_id){
                    $q->where('id', $sourcee_id);
                })->paginate(10);
            }
            else{
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $investissements = Investissement::where('id', 'like', '%'.$term.'%')->orWhere('nom_investissement', 'like', '%'.$term.'%')
                ->with('annee')
                ->with('region')
                ->with('monnaie')
                ->with('structure')
                ->with('source')
                ->with('dimension')
                ->with('bailleurs')
                ->with('piliers')->with('bailleurs')
                ->with('axes')
                ->with('mode_financements')
                ->with('ligne_financements')
                ->with('fichiers')
                ->whereHas('structure', function($q) use ($structure_id){
                    $q->where('id', $structure_id);
                })->paginate(10);
            }
        }
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements", "data" =>$investissements,"total"=> $total]);  
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
                $investissement = Investissement::create(
                    ['state' => 'INITIER_INVESTISSEMENT']
                );
            }
            if ($request->user()->hasRole('admin_structure')){  
                $investissement = Investissement::create(
                    ['state' => 'VALIDATION_ADMIN_STRUCTURE']
                );
            }  
            

            $annee = $input['annee'];
            $monnaie = $input['monnaie'];
            $region = $input['region'];
            $dimension = $input['dimension'];

            $libelleModeFinancements = explode (",", $input['libelleModeFinancements']);
            $montantModeFinancements = explode (",", $input['montantModeFinancements']);

            //$bailleurs = explode (",", $input['bailleurs']); 
            $structure_sources = explode (",", $input['structure_sources']); 
            $structure_beneficiaires = explode (",", $input['structure_beneficiaires']);    
            $regions = explode (",", $input['regions']);    
            $piliers = explode (",", $input['piliers']); 
            $axes = explode (",", $input['axes']); 
            $montantBienServicePrevus = explode (",", $input['montantBienServicePrevus']);
            $montantBienServiceMobilises = explode (",", $input['montantBienServiceMobilises']);
            $montantBienServiceExecutes = explode (",", $input['montantBienServiceExecutes']);
            $montantInvestissementPrevus = explode (",", $input['montantInvestissementPrevus']);
            $montantInvestissementMobilises = explode (",", $input['montantInvestissementMobilises']);
            $montantInvestissementExecutes = explode (",", $input['montantInvestissementExecutes']);

            $tempLigneModeFinancements = str_replace("\\", "",$input['ligneModeFinancements']);
            $ligneModeFinancements = json_decode($tempLigneModeFinancements);
 
            $tempLigneFinancements = str_replace("\\", "",$input['ligneFinancements']);
            $ligneFinancements = json_decode($tempLigneFinancements);

            if(isset($input['libelle_fichiers']) && isset($input['input_fichiers'])){
                $libelle_fichiers = $input['libelle_fichiers'];
                $input_fichiers = $input['input_fichiers'];
            }

            if($structure_id!=null){               
                $structureObj = Structure::where('id',intval($structure_id))->first();
                $investissement->structure()->attach($structureObj);
            }
            if($source_id!=null){               
                $sourceObj = SourceFinancement::where('id',intval($source_id))->first();
                $investissement->source()->attach($sourceObj);
            }

            if($annee!=null){               
                $anneeObj = Annee::where('id',$annee)->first();
                $investissement->annee()->attach($anneeObj);
            }
            if($monnaie!=null){               
                $monnaieObj = Monnaie::where('id',$monnaie)->first();
                $investissement->monnaie()->attach($monnaieObj);
            }
            if($region!=null){               
                $regionObj = Region::where('id',$region)->first();
                $investissement->region()->attach($regionObj);
            }
            if($dimension!=null){               
                $dimensionObj = Dimension::where('id',$dimension)->first();
                $investissement->dimension()->attach($dimensionObj);
            }
            $imode=0;
            if(!empty($libelleModeFinancements)){
                foreach($libelleModeFinancements as $libelleModeFinancement){
                    $ligneModeFinancementObj = ModeFinancement::create([
                        'libelle' => $libelleModeFinancement,
                        'montant' => $montantModeFinancements[$imode],
                        'status' => 'actif'
                    ]);
                    $investissement->mode_financements()->attach($ligneModeFinancementObj);
                    $imode++;
                }
            }
            $ifinance=0;
            if(!empty($piliers)){
                foreach($piliers as $pilier){
                    /* $bailleurObj = Bailleur::where('id',intval($bailleurs[$ifinance]))->first();
                    $investissement_id = $investissement->id;

                    $investissement->bailleurs()->detach($bailleurObj);
                    $investissement->bailleurs()->attach($bailleurObj);
                    */
                    $structure_sourceObj = Structure::where('id',intval($structure_sources[$ifinance]))->first();
                    $type_structure_sourceObj = SourceFinancement::where('id',$structure_sourceObj->source_financements[0]->id)->first();

                    $structure_beneficiaireObj = Structure::where('id',intval($structure_beneficiaires[$ifinance]))->first();
                    $regionObj = Region::where('id',intval($regions[$ifinance]))->first();
                    $pilierObj = Pilier::where('id',intval($pilier))->first();
                    $investissement_id = $investissement->id;
                    $axeObj = Axe::where('id',intval($axes[$ifinance]))->first();
              
                    $anneeObj = Annee::where('id',$annee)->first();
                    $monnaieObj = Monnaie::where('id',$monnaie)->first();            
                    $structureObj = Structure::where('id',$structure_id)->first();            
                    $dimensionObj = Dimension::where('id',$dimension)->first();

                    $ligneFinancementObj = LigneFinancement::create([                      
                        'id_investissement'=> intval($investissement->id), 
                        'id_structure'=> intval($structure_id), 
                        'id_annee'=> intval($annee), 
                        'id_monnaie'=> intval($monnaie), 
                        'id_dimension'=> intval($dimension), 
                        'id_type_structure_source'=> intval($type_structure_sourceObj->id),
                        'id_structure_source'=> intval($structure_sources[$ifinance]), 
                        'id_structure_beneficiaire'=> intval($structure_beneficiaires[$ifinance]), 
                        'id_region'=> intval($regions[$ifinance]), 
                        'id_pilier'=> intval($pilier), 
                        'id_axe'=> intval($axes[$ifinance]), 
                        'montantBienServicePrevus'=> $montantBienServicePrevus[$ifinance],
                        'montantBienServiceMobilises'=> $montantBienServiceMobilises[$ifinance],
                        'montantBienServiceExecutes'=> $montantBienServiceExecutes[$ifinance],
                        'montantInvestissementPrevus'=> $montantInvestissementPrevus[$ifinance],
                        'montantInvestissementMobilises'=> $montantInvestissementMobilises[$ifinance],
                        'montantInvestissementExecutes'=> $montantInvestissementExecutes[$ifinance], 
                        'status' => $investissement->status
                    ]);

                    $ligneFinancementObj->axe()->detach($axeObj);
                    $ligneFinancementObj->axe()->attach($axeObj);

                    $ligneFinancementObj->pilier()->detach($pilierObj);
                    $ligneFinancementObj->pilier()->attach($pilierObj);

                    $ligneFinancementObj->structure_source()->detach($structure_sourceObj);
                    $ligneFinancementObj->structure_source()->attach($structure_sourceObj);

                    $ligneFinancementObj->type_structure_source()->detach($type_structure_sourceObj);
                    $ligneFinancementObj->type_structure_source()->attach($type_structure_sourceObj);

                    $ligneFinancementObj->structure_beneficiaire()->detach($structure_beneficiaireObj);
                    $ligneFinancementObj->structure_beneficiaire()->attach($structure_beneficiaireObj);

                    $ligneFinancementObj->region()->detach($regionObj);
                    $ligneFinancementObj->region()->attach($regionObj);

                    $ligneFinancementObj->investissement()->detach($investissement);
                    $ligneFinancementObj->investissement()->attach($investissement);

                    $ligneFinancementObj->structure()->detach($structureObj);
                    $ligneFinancementObj->structure()->attach($structureObj);

                    $ligneFinancementObj->annee()->detach($anneeObj);
                    $ligneFinancementObj->annee()->attach($anneeObj);

                    $ligneFinancementObj->monnaie()->detach($monnaieObj);
                    $ligneFinancementObj->monnaie()->attach($monnaieObj);

                    $ligneFinancementObj->dimension()->detach($dimensionObj);
                    $ligneFinancementObj->dimension()->attach($dimensionObj);

                    $investissement->ligne_financements()->detach($ligneFinancementObj);
                    $investissement->ligne_financements()->attach($ligneFinancementObj);

                    $ifinance++;
                }
            }

            //Fichiers
            if(isset($input['libelle_fichiers']) && isset($input['input_fichiers'])){
                $ifichier = 0;
                if(!empty($libelle_fichiers)){
                    foreach($libelle_fichiers as $libelle_fichier){
                        if ($input_fichiers[$ifichier] && $input_fichiers[$ifichier]->isValid()) {
                            $upload_path = public_path('upload');
                            $file = $input_fichiers[$ifichier];
                            $file_name = $file->getClientOriginalName();
                            $file_extension = $file->getClientOriginalExtension();
                            $url_file = $upload_path . '/' . $file_name;
                            $generated_new_name = 'accord_siege_' . time() . '.' . $file_extension;
                            $file->move($upload_path, $generated_new_name);
                
                            $fichierObj = Fichier::create([
                                'name' => $libelle_fichiers[$ifichier],
                                'url' => $url_file,
                                'extension' => $file_extension,
                                'description' => 'Fichier'
                            ]);
                            $investissement->fichiers()->attach($fichierObj);
                        }
                        $ifichier++;
                    }
                }
            }
    
            return response()->json(["success" => true, "message" => "Investissement modifié avec succès.", "data" =>$annee]);
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
        $investissement = Investissement::with('region')
        ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('source')
            ->with('dimension')
            ->with('piliers')->with('bailleurs')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')
        ->get()
        ->find($id);
        if (is_null($investissement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "investissement not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "investissement retrieved successfully.", "data" => $investissement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Investissement $investissement)
    {
        $input = $request->all();

        $structure_id = User::find($request->user()->id)->structures[0]->id;
        $source = User::find($request->user()->id)->structures[0]->source_financements[0];
        $source_id = $source->id;
        $source_libelle = $source->libelle_source;

        $validator = Validator::make($input, ['annee' => 'required','monnaie' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        else{           
            //news data
            $annee = $input['annee'];
            $monnaie = $input['monnaie'];
            $region = $input['region'];
            $dimension = $input['dimension'];
            $structure_sources = explode (",", $input['structure_sources']); 
            $structure_beneficiaires = explode (",", $input['structure_beneficiaires']); 
            $regions = explode (",", $input['regions']); 
            $piliers = explode (",", $input['piliers']); 
            $axes = explode (",", $input['axes']); 

            $libelleModeFinancements = explode (",", $input['libelleModeFinancements']);
            $montantModeFinancements = explode (",", $input['montantModeFinancements']);
            $montantBienServicePrevus = explode (",", $input['montantBienServicePrevus']);
            $montantBienServiceMobilises = explode (",", $input['montantBienServiceMobilises']);
            $montantBienServiceExecutes = explode (",", $input['montantBienServiceExecutes']);
            $montantInvestissementPrevus = explode (",", $input['montantInvestissementPrevus']);
            $montantInvestissementMobilises = explode (",", $input['montantInvestissementMobilises']);
            $montantInvestissementExecutes = explode (",", $input['montantInvestissementExecutes']);


            $tempLigneModeFinancements = str_replace("\\", "",$input['ligneModeFinancements']);
            $ligneModeFinancements = json_decode($tempLigneModeFinancements);
 
            $tempLigneFinancements = str_replace("\\", "",$input['ligneFinancements']);
            $ligneFinancements = json_decode($tempLigneFinancements);

            //old data
            $old_structure = $investissement->structure();
            $old_source = $investissement->source();
            $old_annee = $investissement->annee();
            $old_monnaie = $investissement->monnaie();
            $old_region = $investissement->region();
            $old_dimension = $investissement->dimension();
            /* $old_bailleurs = $investissement->bailleurs(); */
            $old_piliers = $investissement->piliers();
            $old_axes = $investissement->axes();
            $old_ligneModeFinancements = $investissement->mode_financements();
            $old_ligneFinancements = $investissement->ligne_financements();
            $old_fichiers = $investissement->fichiers();
            //traitements
            if($structure_id!=null){   
                foreach($old_structure as $structure){
                    $old_structureObj = Structure::where('id',$structure)->first();
                    $investissement->structure()->detach($old_structureObj);
                }           
                $structureObj = Structure::where('id',intval($structure_id))->first();
                $investissement->structure()->attach($structureObj);
            }
            if($source_id!=null){ 
                foreach($old_source as $source){
                    $old_sourceObj = SourceFinancement::where('id',$source)->first();
                    $investissement->source()->detach($old_sourceObj);
                }               
                $sourceObj = SourceFinancement::where('id',intval($source_id))->first();
                $investissement->source()->attach($sourceObj);
            }

            if($annee!=null){   
                foreach($old_annee as $annee){
                    $old_anneeObj = Annee::where('id',$annee)->first();
                    $investissement->annee()->detach($old_anneeObj);
                }            
                $anneeObj = Annee::where('id',intval($input['annee']))->first();
                $investissement->annee()->attach($anneeObj);
            }
            if($monnaie!=null){  
                foreach($old_monnaie as $monnaie){
                    $old_monnaieObj = Monnaie::where('id',$monnaie)->first();
                    $investissement->monnaie()->detach($old_monnaieObj);
                }             
                $monnaieObj = Monnaie::where('id',intval($input['monnaie']))->first();
                $investissement->monnaie()->attach($monnaieObj);
            }
            if($region!=null){  
                foreach($old_region as $region){
                    $old_regionObj = Region::where('id',$region)->first();
                    $investissement->region()->detach($old_regionObj);
                }              
                $regionObj = Region::where('id',intval($input['region']))->first();
                $investissement->region()->attach($regionObj);
            }
            if($dimension!=null){  
                foreach($old_dimension as $dimension){
                    $old_dimensionObj = Dimension::where('id',$dimension)->first();
                    $investissement->dimension()->detach($old_dimensionObj);
                }              
                $dimensionObj = Dimension::where('id',intval($input['dimension']))->first();
                $investissement->dimension()->attach($dimensionObj);
            }
            $imode=0;
            if(!empty($libelleModeFinancements)){
                foreach($old_ligneModeFinancements as $ligneModeFinancement){
                    $old_ligneModeFinancementObj = ModeFinancement::where('id',$ligneModeFinancement)->first();
                    $investissement->mode_financements()->detach($old_ligneModeFinancementObj);
                }
                foreach($libelleModeFinancements as $libelleModeFinancement){
                    $ligneModeFinancementObj = ModeFinancement::create([
                        'libelle' => $libelleModeFinancement,
                        'montant' => $montantModeFinancements[$imode],
                        'status' => 'actif'
                    ]);
                    $investissement->mode_financements()->attach($ligneModeFinancementObj);
                    $imode++;
                }
            }
            $ifinance=0;
            if(!empty($piliers)){
                /* foreach($old_bailleurs as $bailleur){
                    $old_bailleurObj = Bailleur::where('id',$bailleur)->first();
                    $investissement->bailleurs()->detach($old_bailleurObj);
                } */

                foreach($old_ligneFinancements as $ligneFinancement){
                    $old_ligneFinancementObj = LigneFinancement::where('id',$ligneFinancement)->first();
                    $investissement->ligne_financements()->detach($old_ligneFinancementObj);
                }
                foreach($piliers as $pilier){
                    /* $bailleurObj = Bailleur::where('id',intval($bailleurs[$ifinance]))->first();
                    $investissement_id = $investissement->id;

                    $investissement->bailleurs()->detach($bailleurObj);
                    $investissement->bailleurs()->attach($bailleurObj); */

                    $structure_sourceObj = Structure::where('id',intval($structure_sources[$ifinance]))->first();
                    $type_structure_sourceObj = SourceFinancement::where('id',$structure_sourceObj->source_financements[0]->id)->first();
                    $structure_beneficiaireObj = Structure::where('id',intval($structure_beneficiaires[$ifinance]))->first();
                    $regionObj = Region::where('id',intval($regions[$ifinance]))->first();
                    $pilierObj = Pilier::where('id',intval($pilier))->first();
                    $axeObj = Axe::where('id',intval($axes[$ifinance]))->first();
                    $anneeObj = Annee::where('id',$annee)->first();
                    $monnaieObj = Monnaie::where('id',$monnaie)->first();            
                    $structureObj = Structure::where('id',$structure_id)->first();            
                    $dimensionObj = Dimension::where('id',$dimension)->first();

                    $ligneFinancementObj = LigneFinancement::create([                      
                        'id_investissement'=> intval($investissement->id), 
                        'id_structure'=> intval($structure_id), 
                        'id_annee'=> intval($annee), 
                        'id_monnaie'=> intval($monnaie), 
                        'id_dimension'=> intval($dimension), 
                        'id_type_structure_source'=> intval($type_structure_sourceObj->id),
                        'id_structure_source'=> intval($structure_sources[$ifinance]), 
                        'id_structure_beneficiaire'=> intval($structure_beneficiaires[$ifinance]), 
                        'id_region'=> intval($regions[$ifinance]), 
                        'id_pilier'=> intval($pilier), 
                        'id_axe'=> intval($axes[$ifinance]), 
                        'montantBienServicePrevus'=> $montantBienServicePrevus[$ifinance],
                        'montantBienServiceMobilises'=> $montantBienServiceMobilises[$ifinance],
                        'montantBienServiceExecutes'=> $montantBienServiceExecutes[$ifinance],
                        'montantInvestissementPrevus'=> $montantInvestissementPrevus[$ifinance],
                        'montantInvestissementMobilises'=> $montantInvestissementMobilises[$ifinance],
                        'montantInvestissementExecutes'=> $montantInvestissementExecutes[$ifinance], 
                        'status' => $investissement->status
                    ]);
                    $ligneFinancementObj->axe()->detach($axeObj);
                    $ligneFinancementObj->axe()->attach($axeObj);

                    $ligneFinancementObj->pilier()->detach($pilierObj);
                    $ligneFinancementObj->pilier()->attach($pilierObj);

                    $ligneFinancementObj->structure_source()->detach($structure_sourceObj);
                    $ligneFinancementObj->structure_source()->attach($structure_sourceObj);

                    $ligneFinancementObj->type_structure_source()->detach($type_structure_sourceObj);
                    $ligneFinancementObj->type_structure_source()->attach($type_structure_sourceObj);

                    $ligneFinancementObj->structure_beneficiaire()->detach($structure_beneficiaireObj);
                    $ligneFinancementObj->structure_beneficiaire()->attach($structure_beneficiaireObj);

                    $ligneFinancementObj->region()->detach($regionObj);
                    $ligneFinancementObj->region()->attach($regionObj);

                    $ligneFinancementObj->investissement()->detach($investissement);
                    $ligneFinancementObj->investissement()->attach($investissement);

                    $ligneFinancementObj->structure()->detach($structureObj);
                    $ligneFinancementObj->structure()->attach($structureObj);

                    $ligneFinancementObj->annee()->detach($anneeObj);
                    $ligneFinancementObj->annee()->attach($anneeObj);

                    $ligneFinancementObj->monnaie()->detach($monnaieObj);
                    $ligneFinancementObj->monnaie()->attach($monnaieObj);

                    $ligneFinancementObj->dimension()->detach($dimensionObj);
                    $ligneFinancementObj->dimension()->attach($dimensionObj);

                    $investissement->ligne_financements()->detach($ligneFinancementObj);
                    $investissement->ligne_financements()->attach($ligneFinancementObj);


                    $ifinance++;
                }
            }

            //Fichiers
            if(isset($input['libelle_fichiers']) && isset($input['input_fichiers'])){
                $libelle_fichiers = $input['libelle_fichiers'];
                $input_fichiers = $input['input_fichiers'];

                $ifichier = 0;
                if(!empty($libelle_fichiers) && $libelle_fichiers!=null && $input_fichiers!=null){
                    foreach($old_fichiers as $fichier){
                        $old_fichierObj = Fichier::where('id',$fichier)->first();
                        $investissement->fichiers()->detach($old_fichierObj);
                    } 
                    foreach($libelle_fichiers as $libelle_fichier){
                        if ($input_fichiers[$ifichier] && $input_fichiers[$ifichier]->isValid()) {
                            $upload_path = public_path('upload');
                            $file = $input_fichiers[$ifichier];
                            $file_name = $file->getClientOriginalName();
                            $file_extension = $file->getClientOriginalExtension();
                            $url_file = $upload_path . '/' . $file_name;
                            $generated_new_name = 'accord_siege_' . time() . '.' . $file_extension;
                            $file->move($upload_path, $generated_new_name);
                
                            $fichierObj = Fichier::create([
                                'name' => $libelle_fichiers[$ifichier],
                                'url' => $url_file,
                                'extension' => $file_extension,
                                'description' => 'Fichier'
                            ]);
                            $investissement->fichiers()->attach($fichierObj);
                        }
                        $ifichier++;
                    }
                }
            }
    
            return response()->json(["success" => true, "message" => "Investissement enregistré avec succès.", "data" =>$input['annee']]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Investissement $investissement)
    {
        $investissement->delete();
        return response()
            ->json(["success" => true, "message" => "Investissement supprimé.", "data" => $investissement]);
    }


    /////////////////////////////////////////   WORKFLOW / ///////////////////////////
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validation_investissement(Request $request)
    {
        $input = $request->all();
        

        $investissement = Investissement::where('id',$input['id'])->first();

        if ($request->user()->hasRole('point_focal')){
            $investissement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $investissement->status = 'a_valider';
        }
        if ($request->user()->hasRole('admin_structure')){
            $investissement->state = 'VALIDATION_DIRECTEUR_EPS';
            $investissement->status = 'a_valider';

            /* if($investissement->source[0]->libelle_source=='EPS'){
                $investissement->state = 'VALIDATION_DIRECTEUR_EPS';
                $investissement->status = 'a_valider';
            }
            else{
                $investissement->state = 'FIN_PROCESS';
                $investissement->status = 'publie';
            } */
        }
        if ($request->user()->hasRole('directeur_eps')){
            $investissement->state = 'FIN_PROCESS';
            $investissement->status = 'publie';
        }
        $investissement->save();

        return response()->json(["success" => true, "message" => "Investissement validé", "data" =>$investissement]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rejet_investissement(Request $request)
    {
        $input = $request->all();
        $motif_rejet = $input['motif_rejet'];
        

        $investissement = Investissement::where('id',$input['id'])->first();

        if ($request->user()->hasRole('admin_structure')){          
            $investissement->state = 'INITIER_INVESTISSEMENT';
            $investissement->status = 'rejete';          
            $investissement->motif_rejet = $motif_rejet;          
        }
        if ($request->user()->hasRole('directeur_eps')){
            $investissement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $investissement->status = 'rejete';
            $investissement->motif_rejet = $motif_rejet; 
        }
        if ($request->user()->hasRole('admin_dprs')){
            $investissement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $investissement->status = 'rejete';
            $investissement->motif_rejet = $motif_rejet; 
        }
        $investissement->save();

        return response()->json(["success" => true, "message" => "Investissement rejeté avec succés", "data" =>$investissement]);  
    }
}
