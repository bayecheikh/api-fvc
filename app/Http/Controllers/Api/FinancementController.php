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
use Illuminate\Support\Facades\DB;

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
    public function financementMultipleSearch(Request $request, $term)
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
            ->with('structure')->where('status', 'like', '%publie%')
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
                ->with('fichiers')->where('status', 'like', '%publie%')
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
    $user = $request->user();
    $structure_id = $user->structures[0]->id ?? null;

    $validator = Validator::make($input, ['annee' => 'required']);
    if ($validator->fails()) {
        return response()->json($validator->errors());
    }

    // Création du financement selon le rôle
    $state = $user->hasRole('point_focal') ? 'INITIER_INVESTISSEMENT' : ($user->hasRole('admin_structure') ? 'VALIDATION_ADMIN_STRUCTURE' : null);

    $financement = Financement::create([
        'date_debut' => $input['date_debut'] ?? null,
        'date_fin' => $input['date_fin'] ?? null,
        'titre_projet' => $input['titre_projet'] ?? null,
        'objectif_global_projet' => $input['objectif_global_projet'] ?? null,
        'montant_total_adaptation' => $input['montant_total_adaptation'] ?? 0,
        'montant_total_attenuation' => $input['montant_total_attenuation'] ?? 0,
        'montant_total_execute' => $input['montant_total_execute'] ?? 0,
        'montant_total_restant' => $input['montant_total_restant'] ?? 0,
        'renforcement_capacite' => $input['renforcement_capacite'] ?? null,
        'transfert_technologie' => $input['transfert_technologie'] ?? null,
        'montant_total' => $input['montant_total'] ?? 0,
        'nombre_beneficiaire' => $input['nombre_beneficiaire'] ?? 0,
        'volume_co2' => $input['volume_co2'] ?? 0,
        'state' => $state,
        'status' => 'brouillon'
    ]);

    // Structure
    if ($structure_id) {
        $structureObj = Structure::find($structure_id);
        if ($structureObj) $financement->structure()->attach($structureObj);
    }

    // Année
    if (!empty($input['annee'])) {
        $anneeObj = Annee::find($input['annee']);
        if ($anneeObj) $financement->annee()->attach($anneeObj);
    }

    // Lignes financement secteurs
    if (!empty($input['ligne_financement_secteurs'])) {
        $ligneFinancementSecteurs = json_decode(str_replace("\\", "", $input['ligne_financement_secteurs']), true);
        if (!empty($ligneFinancementSecteurs)) {
            foreach ($ligneFinancementSecteurs as $ligne) {
                $ligneObj = LigneFinancementSecteur::create([
                    'id_investissement' => $financement->id,
                    'id_secteur' => $ligne['secteur'] ?? null,
                    'id_sous_secteur' => $ligne['sous_secteur'] ?? null,
                    'montant_total' => $ligne['montant_total'] ?? 0,
                    'status' => $financement->status
                ]);
                $financement->ligne_financement_secteurs()->attach($ligneObj);
            }
        }
    }

    // Lignes financement zones
    if (!empty($input['ligne_financement_zones'])) {
        $ligneFinancementZones = json_decode(str_replace("\\", "", $input['ligne_financement_zones']), true);
        if (!empty($ligneFinancementZones)) {
            foreach ($ligneFinancementZones as $ligne) {
                $ligneObj = LigneFinancementZone::create([
                    'id_investissement' => $financement->id,
                    'id_region' => $ligne['region'] ?? null,
                    'montant_total' => $ligne['montant_total'] ?? 0,
                    'status' => $financement->status
                ]);
                $financement->ligne_financement_zones()->attach($ligneObj);
            }
        }
    }

    // Lignes financement bailleurs
    if (!empty($input['ligne_financement_bailleurs'])) {
        $ligneFinancementBailleurs = json_decode(str_replace("\\", "", $input['ligne_financement_bailleurs']), true);
        if (!empty($ligneFinancementBailleurs)) {
            foreach ($ligneFinancementBailleurs as $ligne) {
                $ligneObj = LigneFinancementBailleur::create([
                    'id_investissement' => $financement->id,
                    'id_bailleur' => $ligne['bailleur'] ?? null,
                    'id_instrumet_financier' => $ligne['instrumet_financier'] ?? null,
                    'montant_total' => $ligne['montant_total'] ?? 0,
                    'status' => $financement->status
                ]);
                $financement->ligne_financement_bailleurs()->attach($ligneObj);
            }
        }
    }

    // Lignes financement co-financeurs
    if (!empty($input['ligne_financement_cos'])) {
        $ligneFinancementCos = json_decode(str_replace("\\", "", $input['ligne_financement_cos']), true);
        if (!empty($ligneFinancementCos)) {
            foreach ($ligneFinancementCos as $ligne) {
                $ligneObj = LigneFinancementCo::create([
                    'id_investissement' => $financement->id,
                    'id_instrument_financier' => $ligne['instrument_financier'] ?? null,
                    'nom_co_financier' => $ligne['nom_co_financier'] ?? null,
                    'montant_co_financier' => $ligne['montant_co_financier'] ?? 0,
                    'status' => $financement->status
                ]);
                $financement->ligne_financement_cos()->attach($ligneObj);
            }
        }
    }

    return response()->json([
        "success" => true,
        "message" => "Financement ajouté avec succès.",
        "data" => $financement
    ]);
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
    $user = $request->user();
    $structure_id = $user->structures[0]->id ?? null;
    $source = $user->structures[0]->source_financements[0] ?? null;
    $source_id = $source->id ?? null;

    $validator = Validator::make($input, ['annee' => 'nullable','monnaie' => 'nullable']);
    if ($validator->fails()) {
        return response()->json($validator->errors());
    }

    $financement->update([
    'titre_projet' => $input['titre_projet'] ?? $financement->titre_projet,
    'objectif_global_projet' => $input['objectif_global_projet'] ?? $financement->objectif_global_projet,
    'date_debut' => $input['date_debut'] ?? $financement->date_debut,
    'date_fin' => $input['date_fin'] ?? $financement->date_fin,
    'montant_total_adaptation' => $input['montant_total_adaptation'] ?? $financement->montant_total_adaptation,
    'montant_total_attenuation' => $input['montant_total_attenuation'] ?? $financement->montant_total_attenuation,
    'montant_total_execute' => $input['montant_total_execute'] ?? $financement->montant_total_execute,
    'montant_total_restant' => $input['montant_total_restant'] ?? $financement->montant_total_restant,
    'nombre_beneficiaire' => $input['nombre_beneficiaire'] ?? $financement->nombre_beneficiaire,
    'volume_co2' => $input['volume_co2'] ?? $financement->volume_co2,
]);

    // Nouvelles données
    $annee = $input['annee'] ?? null;
    $monnaie = $input['monnaie'] ?? null;
    $region = $input['region'] ?? null;
    $dimension = $input['dimension'] ?? null;
    $structure_sources = explode(",", $input['structure_sources'] ?? '');
    $structure_beneficiaires = explode(",", $input['structure_beneficiaires'] ?? '');
    $regions = explode(",", $input['regions'] ?? '');
    $piliers = explode(",", $input['piliers'] ?? '');
    $axes = explode(",", $input['axes'] ?? '');
    $libelleModeFinancements = explode(",", $input['libelleModeFinancements'] ?? '');
    $montantModeFinancements = explode(",", $input['montantModeFinancements'] ?? '');
    $montantBienServicePrevus = explode(",", $input['montantBienServicePrevus'] ?? '');
    $montantBienServiceMobilises = explode(",", $input['montantBienServiceMobilises'] ?? '');
    $montantBienServiceExecutes = explode(",", $input['montantBienServiceExecutes'] ?? '');
    $montantfinancementPrevus = explode(",", $input['montantfinancementPrevus'] ?? '');
    $montantfinancementMobilises = explode(",", $input['montantfinancementMobilises'] ?? '');
    $montantfinancementExecutes = explode(",", $input['montantfinancementExecutes'] ?? '');

    // Anciennes relations
    $old_structure = $financement->structure()->exists() ? $financement->structure : null;
    $old_source = $financement->source()->exists() ? $financement->source : null;
    $old_annee = $financement->annee()->exists() ? $financement->annee : null;
    $old_monnaie = $financement->monnaie()->exists() ? $financement->monnaie : null;
    $old_region = $financement->region()->exists() ? $financement->region : null;
    $old_dimension = $financement->dimension()->exists() ? $financement->dimension : null;
    $old_ligneModeFinancements = $financement->mode_financements()->exists() ? $financement->mode_financements : [];
    $old_ligneFinancements = $financement->ligne_financements()->exists() ? $financement->ligne_financements : [];
    $old_fichiers = $financement->fichiers()->exists() ? $financement->fichiers : [];

    // Update structure
    if ($structure_id) {
        if ($old_structure) $financement->structure()->detach($old_structure);
        $structureObj = Structure::find($structure_id);
        if ($structureObj) $financement->structure()->attach($structureObj);
    }

    // Update source
    if ($source_id) {
        if ($old_source) $financement->source()->detach($old_source);
        $sourceObj = SourceFinancement::find($source_id);
        if ($sourceObj) $financement->source()->attach($sourceObj);
    }

    // Update année
    if ($annee) {
        if ($old_annee) $financement->annee()->detach($old_annee);
        $anneeObj = Annee::find($annee);
        if ($anneeObj) $financement->annee()->attach($anneeObj);
    }

    // Update monnaie
    if ($monnaie) {
        if ($old_monnaie) $financement->monnaie()->detach($old_monnaie);
        $monnaieObj = Monnaie::find($monnaie);
        if ($monnaieObj) $financement->monnaie()->attach($monnaieObj);
    }

    // Update région
    if ($region) {
        if ($old_region) $financement->region()->detach($old_region);
        $regionObj = Region::find($region);
        if ($regionObj) $financement->region()->attach($regionObj);
    }

    // Update dimension
    if ($dimension) {
        if ($old_dimension) $financement->dimension()->detach($old_dimension);
        $dimensionObj = Dimension::find($dimension);
        if ($dimensionObj) $financement->dimension()->attach($dimensionObj);
    }

    // Mode financements
    $imode = 0;
    if (!empty($libelleModeFinancements)) {
        foreach ($old_ligneModeFinancements as $oldMode) {
            $financement->mode_financements()->detach($oldMode);
        }
        foreach ($libelleModeFinancements as $libelle) {
            $ligneObj = ModeFinancement::create([
                'libelle' => $libelle,
                'montant' => $montantModeFinancements[$imode] ?? 0,
                'status' => 'actif'
            ]);
            $financement->mode_financements()->attach($ligneObj);
            $imode++;
        }
    }

    // Ligne financements piliers / axes
    $ifinance = 0;
    if (!empty($piliers)) {
        foreach ($old_ligneFinancements as $oldLigne) $financement->ligne_financements()->detach($oldLigne);
        foreach ($piliers as $pilier) {
            $structure_sourceObj = Structure::find($structure_sources[$ifinance] ?? null);
            $structure_beneficiaireObj = Structure::find($structure_beneficiaires[$ifinance] ?? null);
            $regionObj = Region::find($regions[$ifinance] ?? null);
            $pilierObj = Pilier::find($pilier);
            $axeObj = Axe::find($axes[$ifinance] ?? null);

            $ligneFinancementObj = LigneFinancement::create([
                'id_financement' => $financement->id,
                'id_structure' => $structure_id,
                'id_annee' => $annee,
                'id_monnaie' => $monnaie,
                'id_dimension' => $dimension,
                'id_type_structure_source' => $structure_sourceObj->source_financements[0]->id ?? null,
                'id_structure_source' => $structure_sources[$ifinance] ?? null,
                'id_structure_beneficiaire' => $structure_beneficiaires[$ifinance] ?? null,
                'id_region' => $regions[$ifinance] ?? null,
                'id_pilier' => $pilier,
                'id_axe' => $axes[$ifinance] ?? null,
                'montantBienServicePrevus' => $montantBienServicePrevus[$ifinance] ?? 0,
                'montantBienServiceMobilises' => $montantBienServiceMobilises[$ifinance] ?? 0,
                'montantBienServiceExecutes' => $montantBienServiceExecutes[$ifinance] ?? 0,
                'montantfinancementPrevus' => $montantfinancementPrevus[$ifinance] ?? 0,
                'montantfinancementMobilises' => $montantfinancementMobilises[$ifinance] ?? 0,
                'montantfinancementExecutes' => $montantfinancementExecutes[$ifinance] ?? 0,
                'status' => $financement->status
            ]);

            if ($axeObj) {
                $ligneFinancementObj->axe()->sync([$axeObj->id]);
            }
            if ($pilierObj) {
                $ligneFinancementObj->pilier()->sync([$pilierObj->id]);
            }
            if ($structure_sourceObj) {
                $ligneFinancementObj->structure_source()->sync([$structure_sourceObj->id]);
            }
            if ($structure_beneficiaireObj) {
                $ligneFinancementObj->structure_beneficiaire()->sync([$structure_beneficiaireObj->id]);
            }
            if ($regionObj) {
                $ligneFinancementObj->region()->sync([$regionObj->id]);
            }

            $ifinance++;
        }
    }

    // Fichiers
    if (!empty($input['libelle_fichiers']) && !empty($input['input_fichiers'])) {
        foreach ($old_fichiers as $fichier) $financement->fichiers()->detach($fichier);

        foreach ($input['libelle_fichiers'] as $i => $libelle) {
            $file = $input['input_fichiers'][$i] ?? null;
            if ($file && $file->isValid()) {
                $upload_path = public_path('upload');
                $file_extension = $file->getClientOriginalExtension();
                $generated_new_name = 'accord_siege_' . time() . '.' . $file_extension;
                $file->move($upload_path, $generated_new_name);

                $fichierObj = Fichier::create([
                    'name' => $libelle,
                    'url' => $upload_path . '/' . $generated_new_name,
                    'extension' => $file_extension,
                    'description' => 'Fichier'
                ]);
                $financement->fichiers()->attach($fichierObj);
            }
        }
    }

    return response()->json([
        "success" => true,
        "message" => "Financement mis à jour avec succès.",
        "data" => $financement
    ]);
}



/* ==============================
 * Helper JSON lines
 * ============================== */
private function handleJsonLines(?string $json, callable $creator, callable $attacher)
{
    if (!$json) return;
    $data = json_decode(str_replace("\\", "", $json), true);
    if (!is_array($data)) throw new \Exception('Format JSON invalide.');
    foreach($data as $line){
        if(!is_array($line)) continue;
        $obj = $creator($line);
        $attacher($obj);
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
