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

use App\Models\Financement;
use App\Models\User;
use App\Models\Fichier;
use App\Models\Structure;
use App\Models\Annee;
use App\Models\Monnaie;
use App\Models\LigneFinancement;
use App\Models\LigneSecteur;
use App\Models\ModeFinancement;
use App\Models\LigneModeFinancement;
use App\Models\Dimension;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Bailleur;
use App\Models\Pilier;
use App\Models\Axe;

class FinancementController extends Controller
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
        if ($request->user()->hasRole('super_admin')) {
            $financements = Financement::with('annee')
            ->with('domaine_financement')
            ->with('source_financement')
            ->with('objectif_adaptations')
            ->with('objectif_attenuations')
            ->with('objectif_transversals')
            ->with('agence_acredite')
            ->with('ligne_financement_bailleurs')
            ->with('ligne_financement_cos')
            ->with('ligne_financement_secteurs')
            ->with('ligne_financement_zones')
            ->with('resumes')
            ->with('tableau_budgets')
            ->with('structure')
            ->paginate(20);
        }
        else{
            if($request->user()->hasRole('directeur_eps')){
                $financements = Financement::with('annee')
                ->with('domaine_financement')
                ->with('source_financement')
                ->with('objectif_adaptations')
                ->with('objectif_attenuations')
                ->with('objectif_transversals')
                ->with('agence_acredite')
                ->with('ligne_financement_bailleurs')
                ->with('ligne_financement_cos')
                ->with('ligne_financement_secteurs')
                ->with('ligne_financement_zones')
                ->with('resumes')
                ->with('tableau_budgets')
                ->with('structure')
                ->orderBy('created_at', 'DESC')->paginate(20);
            }
            else{
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $financements = Financement::with('annee')
                ->with('domaine_financement')
                ->with('source_financement')
                ->with('objectif_adaptations')
                ->with('objectif_attenuations')
                ->with('objectif_transversals')
                ->with('agence_acredite')
                ->with('ligne_financement_bailleurs')
                ->with('ligne_financement_cos')
                ->with('ligne_financement_secteurs')
                ->with('ligne_financement_zones')
                ->with('resumes')
                ->with('tableau_budgets')
                ->with('structure')
                ->whereHas('structure', function($q) use ($structure_id){
                    $q->where('id', $structure_id);
                })->orderBy('created_at', 'DESC')->paginate(20);
            }

        }


        $total = $financements->total();
        return response()->json(["success" => true, "message" => "Structures List", "data" =>$financements,"total"=> $total]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function financementMultipleSearch($term)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $financements = Financement::where('id', 'like', '%'.$term.'%')->orWhere('nom_financement', 'like', '%'.$term.'%')
            ->with('annee')
            ->with('domaine_financement')
            ->with('source_financement')
            ->with('objectif_adaptations')
            ->with('objectif_attenuations')
            ->with('objectif_transversals')
            ->with('agence_acredite')
            ->with('ligne_financement_bailleurs')
            ->with('ligne_financement_cos')
            ->with('ligne_financement_secteurs')
            ->with('ligne_financement_zones')
            ->with('resumes')
            ->with('tableau_budgets')
            ->with('structure')
            ->paginate(20);
        }else{
            $structure_id = User::find($request->user()->id)->structures[0]->id;
            $financements = Financement::where('id', 'like', '%'.$term.'%')->orWhere('nom_financement', 'like', '%'.$term.'%')
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
            ->paginate(20);

            if($request->user()->hasRole('directeur_eps')){
                $financements = Financement::where('id', 'like', '%'.$term.'%')->orWhere('nom_financement', 'like', '%'.$term.'%')
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
                ->paginate(20);
            }
            else{
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $financements = Financement::where('id', 'like', '%'.$term.'%')->orWhere('nom_financement', 'like', '%'.$term.'%')
                ->with('annee')
                ->with('domaine_financement')
                ->with('source_financement')
                ->with('objectif_adaptations')
                ->with('objectif_attenuations')
                ->with('objectif_transversals')
                ->with('agence_acredite')
                ->with('ligne_financement_bailleurs')
                ->with('ligne_financement_cos')
                ->with('ligne_financement_secteurs')
                ->with('ligne_financement_zones')
                ->with('resumes')
                ->with('tableau_budgets')
                ->with('structure')
                ->whereHas('structure', function($q) use ($structure_id){
                    $q->where('id', $structure_id);
                })->paginate(20);
            }
        }
        $total = $financements->total();
        return response()->json(["success" => true, "message" => "Liste des financements", "data" =>$financements,"total"=> $total]);
    }
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $structure_id = User::find($request->user()->id)->structures[0]->id;


        $validator = Validator::make($input, ['annee' => 'required']);
        if ($validator->fails())
        {
            return response()
            ->json($validator->errors());
        }
        else{
            if ($request->user()->hasRole('point_focal')){
                $financement = Financement::create(
                    [
                        'date_debut'=>$input['date_debut'],
                        'date_fin'=>$input['date_fin'],
                        'titre_projet'=>$input['titre_projet'],
                        'objectif_global_projet'=>$input['objectif_global_projet'],
                        'montant_total_adaptation'=>$input['montant_total_adaptation'],
                        'montant_total_attenuation'=>$input['montant_total_attenuation'],
                        'montant_total_execute'=>$input['montant_total_execute'],
                        'montant_total_restant'=>$input['montant_total_restant'],
                        'renforcement_capacite'=>$input['renforcement_capacite'],
                        'transfert_technologie'=>$input['transfert_technologie'],
                        'montant_total'=>$input['montant_total'],
                        'nombre_beneficiaire'=>$input['nombre_beneficiaire'],
                        'volume_co2'=>$input['volume_co2'],
                        'state' => 'INITIER_INVESTISSEMENT',
                        'status' => 'brouillon'
                    ]
                );
            }
            if ($request->user()->hasRole('admin_structure')){
                $financement = Financement::create(
                    ['date_debut'=>$input['date_debut'],
                    'date_fin'=>$input['date_fin'],
                    'titre_projet'=>$input['titre_projet'],
                    'objectif_global_projet'=>$input['objectif_global_projet'],
                    'montant_total_adaptation'=>$input['montant_total_adaptation'],
                    'montant_total_attenuation'=>$input['montant_total_attenuation'],
                    'montant_total_execute'=>$input['montant_total_execute'],
                    'montant_total_restant'=>$input['montant_total_restant'],
                    'renforcement_capacite'=>$input['renforcement_capacite'],
                    'transfert_technologie'=>$input['transfert_technologie'],
                    'montant_total'=>$input['montant_total'],
                    'nombre_beneficiaire'=>$input['nombre_beneficiaire'],
                    'volume_co2'=>$input['volume_co2'],
                        'state' => 'VALIDATION_ADMIN_STRUCTURE',
                        'status' => 'brouillon'
                    ]
                );
            }

            if($structure_id!=null){
                $structureObj = Structure::where('id',intval($structure_id))->first();
                $financement->structure()->attach($structureObj);
            }
            if($input['annee']!=null){
                $anneeObj = Annee::where('id',$input['annee'])->first();
                $financement->annee()->attach($anneeObj);
            }

            if($input['ligne_financement_secteurs']!=null){
                $tempLigneFinancementSecteurs = str_replace("\\", "",$input['ligne_financement_secteurs']);
                $ligneFinancementSecteurs = json_decode($tempLigneFinancementSecteurs);


                $ifinance=0;
                if(!empty($ligneFinancementSecteurs)){
                    foreach($ligneFinancementSecteurs as $ligneFinancementSecteur){

                        $ligneFinancementSecteurObj = LigneFinancementSecteur::create([
                            'id_investissement'=> intval($financement->id),
                            'id_secteur'=> intval($ligneFinancementSecteur['secteur']),
                            'id_sous_secteur'=> intval($ligneFinancementSecteur['sous_secteur']),
                            'montant_total'=> $ligneFinancementSecteur['montant_total'] ,
                            'status' => $financement->status
                        ]);
                        $financement->ligne_financement_secteurs()->attach($ligneFinancementSecteurObj);

                        $ifinance++;
                    }
                }
            }

            if($input['ligne_financement_zones']!=null){
                $tempLigneFinancementZones = str_replace("\\", "",$input['ligne_financement_zones']);
                $ligneFinancementZones = json_decode($tempLigneFinancementZones);


                $ifinance=0;
                if(!empty($ligneFinancementZones)){
                    foreach($ligneFinancementZones as $ligneFinancementZone){

                        $ligneFinancementZoneObj = LigneFinancementZone::create([
                            'id_investissement'=> intval($financement->id),
                            'id_region'=> intval($ligneFinancementZone['region']),
                            'montant_total'=> $ligneFinancementZone['montant_total'] ,
                            'status' => $financement->status
                        ]);
                        $financement->ligne_financement_zones()->attach($ligneFinancementZoneObj);

                        $ifinance++;
                    }
                }
            }

            if($input['ligne_financement_bailleurs']!=null){
                $tempLigneFinancementBailleurs = str_replace("\\", "",$input['ligne_financement_bailleurs']);
                $ligneFinancementBailleurs = json_decode($tempLigneFinancementBailleurs);


                $ifinance=0;
                if(!empty($ligneFinancementBailleurs)){
                    foreach($ligneFinancementBailleurs as $ligneFinancementBailleur){

                        $ligneFinancementBailleurObj = LigneFinancementBailleur::create([
                            'id_investissement'=> intval($financement->id),
                            'id_bailleur'=> intval($ligneFinancementBailleur['bailleur']),
                            'id_instrumet_financier'=> intval($ligneFinancementBailleur['instrumet_financier']),
                            'montant_total'=> $ligneFinancementBailleur['montant_total'] ,
                            'status' => $financement->status
                        ]);
                        $financement->ligne_financement_bailleurs()->attach($ligneFinancementBailleurObj);

                        $ifinance++;
                    }
                }
            }

            if($input['ligne_financement_cos']!=null){
                $tempLigneFinancementCos = str_replace("\\", "",$input['ligne_financement_cos']);
                $ligneFinancementCos = json_decode($tempLigneFinancementCos);


                $ifinance=0;
                if(!empty($ligneFinancementCos)){
                    foreach($ligneFinancementCos as $ligneFinancementCo){

                        $ligneFinancementCoObj = LigneFinancementCo::create([
                            'id_investissement'=> intval($financement->id),
                            'id_instrument_financier'=> intval($ligneFinancementCo['instrument_financier']),
                            'nom_co_financier'=> intval($ligneFinancementCo['nom_co_financier']),
                            'montant_co_financier'=> $ligneFinancementCo['montant_co_financier'] ,
                            'status' => $financement->status
                        ]);
                        $financement->ligne_financement_cos()->attach($ligneFinancementCoObj);

                        $ifinance++;
                    }
                }
            }

            //Fichiers
            /* if(isset($input['libelle_fichiers']) && isset($input['input_fichiers'])){
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
                            $financement->fichiers()->attach($fichierObj);
                        }
                        $ifichier++;
                    }
                }
            } */

            return response()->json(["success" => true, "message" => "financement ajouté avec succès.", "data" =>$ligneFinancementSecteurs]);
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
        $financement = Financement::with('annee')
        ->with('domaine_financement')
        ->with('source_financement')
        ->with('objectif_adaptations')
        ->with('objectif_attenuations')
        ->with('objectif_transversals')
        ->with('agence_acredite')
        ->with('ligne_financement_bailleurs')
        ->with('ligne_financement_cos')
        ->with('ligne_financement_secteurs')
        ->with('ligne_financement_zones')
        ->with('resumes')
        ->with('tableau_budgets')
        ->with('structure')
        ->get()
        ->find($id);
        if (is_null($financement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "financement not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "financement retrieved successfully.", "data" => $financement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Financement $financement)
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
            $montantfinancementPrevus = explode (",", $input['montantfinancementPrevus']);
            $montantfinancementMobilises = explode (",", $input['montantfinancementMobilises']);
            $montantfinancementExecutes = explode (",", $input['montantfinancementExecutes']);


            $tempLigneModeFinancements = str_replace("\\", "",$input['ligneModeFinancements']);
            $ligneModeFinancements = json_decode($tempLigneModeFinancements);

            $tempLigneFinancements = str_replace("\\", "",$input['ligneFinancements']);
            $ligneFinancements = json_decode($tempLigneFinancements);

            //old data
            $old_structure = $financement->structure();
            $old_source = $financement->source();
            $old_annee = $financement->annee();
            $old_monnaie = $financement->monnaie();
            $old_region = $financement->region();
            $old_dimension = $financement->dimension();
            /* $old_bailleurs = $financement->bailleurs(); */
            $old_piliers = $financement->piliers();
            $old_axes = $financement->axes();
            $old_ligneModeFinancements = $financement->mode_financements();
            $old_ligneFinancements = $financement->ligne_financements();
            $old_fichiers = $financement->fichiers();
            //traitements
            if($structure_id!=null){
                foreach($old_structure as $structure){
                    $old_structureObj = Structure::where('id',$structure)->first();
                    $financement->structure()->detach($old_structureObj);
                }
                $structureObj = Structure::where('id',intval($structure_id))->first();
                $financement->structure()->attach($structureObj);
            }
            if($source_id!=null){
                foreach($old_source as $source){
                    $old_sourceObj = SourceFinancement::where('id',$source)->first();
                    $financement->source()->detach($old_sourceObj);
                }
                $sourceObj = SourceFinancement::where('id',intval($source_id))->first();
                $financement->source()->attach($sourceObj);
            }

            if($annee!=null){
                foreach($old_annee as $annee){
                    $old_anneeObj = Annee::where('id',$annee)->first();
                    $financement->annee()->detach($old_anneeObj);
                }
                $anneeObj = Annee::where('id',intval($input['annee']))->first();
                $financement->annee()->attach($anneeObj);
            }
            if($monnaie!=null){
                foreach($old_monnaie as $monnaie){
                    $old_monnaieObj = Monnaie::where('id',$monnaie)->first();
                    $financement->monnaie()->detach($old_monnaieObj);
                }
                $monnaieObj = Monnaie::where('id',intval($input['monnaie']))->first();
                $financement->monnaie()->attach($monnaieObj);
            }
            if($region!=null){
                foreach($old_region as $region){
                    $old_regionObj = Region::where('id',$region)->first();
                    $financement->region()->detach($old_regionObj);
                }
                $regionObj = Region::where('id',intval($input['region']))->first();
                $financement->region()->attach($regionObj);
            }
            if($dimension!=null){
                foreach($old_dimension as $dimension){
                    $old_dimensionObj = Dimension::where('id',$dimension)->first();
                    $financement->dimension()->detach($old_dimensionObj);
                }
                $dimensionObj = Dimension::where('id',intval($input['dimension']))->first();
                $financement->dimension()->attach($dimensionObj);
            }
            $imode=0;
            if(!empty($libelleModeFinancements)){
                foreach($old_ligneModeFinancements as $ligneModeFinancement){
                    $old_ligneModeFinancementObj = ModeFinancement::where('id',$ligneModeFinancement)->first();
                    $financement->mode_financements()->detach($old_ligneModeFinancementObj);
                }
                foreach($libelleModeFinancements as $libelleModeFinancement){
                    $ligneModeFinancementObj = ModeFinancement::create([
                        'libelle' => $libelleModeFinancement,
                        'montant' => $montantModeFinancements[$imode],
                        'status' => 'actif'
                    ]);
                    $financement->mode_financements()->attach($ligneModeFinancementObj);
                    $imode++;
                }
            }
            $ifinance=0;
            if(!empty($piliers)){
                /* foreach($old_bailleurs as $bailleur){
                    $old_bailleurObj = Bailleur::where('id',$bailleur)->first();
                    $financement->bailleurs()->detach($old_bailleurObj);
                } */

                foreach($old_ligneFinancements as $ligneFinancement){
                    $old_ligneFinancementObj = LigneFinancement::where('id',$ligneFinancement)->first();
                    $financement->ligne_financements()->detach($old_ligneFinancementObj);
                }
                foreach($piliers as $pilier){
                    /* $bailleurObj = Bailleur::where('id',intval($bailleurs[$ifinance]))->first();
                    $financement_id = $financement->id;

                    $financement->bailleurs()->detach($bailleurObj);
                    $financement->bailleurs()->attach($bailleurObj); */

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
                        'id_financement'=> intval($financement->id),
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
                        'montantfinancementPrevus'=> $montantfinancementPrevus[$ifinance],
                        'montantfinancementMobilises'=> $montantfinancementMobilises[$ifinance],
                        'montantfinancementExecutes'=> $montantfinancementExecutes[$ifinance],
                        'status' => $financement->status
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

                    $ligneFinancementObj->financement()->detach($financement);
                    $ligneFinancementObj->financement()->attach($financement);

                    $ligneFinancementObj->structure()->detach($structureObj);
                    $ligneFinancementObj->structure()->attach($structureObj);

                    $ligneFinancementObj->annee()->detach($anneeObj);
                    $ligneFinancementObj->annee()->attach($anneeObj);

                    $ligneFinancementObj->monnaie()->detach($monnaieObj);
                    $ligneFinancementObj->monnaie()->attach($monnaieObj);

                    $ligneFinancementObj->dimension()->detach($dimensionObj);
                    $ligneFinancementObj->dimension()->attach($dimensionObj);

                    $financement->ligne_financements()->detach($ligneFinancementObj);
                    $financement->ligne_financements()->attach($ligneFinancementObj);


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
                        $financement->fichiers()->detach($old_fichierObj);
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
                            $financement->fichiers()->attach($fichierObj);
                        }
                        $ifichier++;
                    }
                }
            }

            return response()->json(["success" => true, "message" => "financement enregistré avec succès.", "data" =>$input['annee']]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(financement $financement)
    {
        $financement->delete();
        return response()
            ->json(["success" => true, "message" => "financement supprimé.", "data" => $financement]);
    }


    /////////////////////////////////////////   WORKFLOW / ///////////////////////////
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validation_financement(Request $request)
    {
        $input = $request->all();


        $financement = Financement::where('id',$input['id'])->first();

        if ($request->user()->hasRole('point_focal')){
            $financement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $financement->status = 'a_valider';
        }
        if ($request->user()->hasRole('admin_structure')){
            $financement->state = 'FIN_PROCESS';
            $financement->status = 'publie';
            /* if($financement->source[0]->libelle_source=='EPS'){
                $financement->state = 'VALIDATION_DIRECTEUR_EPS';
                $financement->status = 'a_valider';
            }
            else{
                $financement->state = 'FIN_PROCESS';
                $financement->status = 'publie';
            } */
        }
        if ($request->user()->hasRole('directeur_eps')){
            $financement->state = 'FIN_PROCESS';
            $financement->status = 'publie';
        }
        $financement->save();

        return response()->json(["success" => true, "message" => "financement validé", "data" =>$financement]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rejet_financement(Request $request)
    {
        $input = $request->all();
        $motif_rejet = $input['motif_rejet'];


        $financement = Financement::where('id',$input['id'])->first();

        if ($request->user()->hasRole('admin_structure')){
            $financement->state = 'INITIER_INVESTISSEMENT';
            $financement->status = 'rejete';
            $financement->motif_rejet = $motif_rejet;
        }
        if ($request->user()->hasRole('directeur_eps')){
            $financement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $financement->status = 'rejete';
            $financement->motif_rejet = $motif_rejet;
        }
        if ($request->user()->hasRole('admin_dprs')){
            $financement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $financement->status = 'rejete';
            $financement->motif_rejet = $motif_rejet;
        }
        $financement->save();

        return response()->json(["success" => true, "message" => "financement rejeté avec succés", "data" =>$financement]);
    }
}
