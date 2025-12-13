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
    $structure = $user->structures->first();
    $structure_id = $structure->id ?? null;

    $validator = Validator::make($input, ['annee' => 'required']);
    if ($validator->fails()) {
        return response()->json($validator->errors());
    }

    // Création du financement selon le rôle
    if ($user->hasRole('point_focal')) {
        $state = 'INITIER_INVESTISSEMENT';
    } elseif ($user->hasRole('admin_structure')) {
        $state = 'VALIDATION_ADMIN_STRUCTURE';
    } else {
        $state = 'BLOQUE';
    }

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

    // Attacher structure si existante
    if ($structure_id) {
        $structureObj = Structure::find($structure_id);
        if ($structureObj) {
            $financement->structure()->attach($structureObj);
        }
    }

    // Attacher année
    if (!empty($input['annee'])) {
        $anneeObj = Annee::find($input['annee']);
        if ($anneeObj) {
            $financement->annee()->attach($anneeObj);
        }
    }

    // Lignes financement secteurs
    if (!empty($input['ligne_financement_secteurs'])) {
        $ligneFinancementSecteurs = json_decode(str_replace("\\", "", $input['ligne_financement_secteurs']), true);
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

    // Lignes financement zones
    if (!empty($input['ligne_financement_zones'])) {
        $ligneFinancementZones = json_decode(str_replace("\\", "", $input['ligne_financement_zones']), true);
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

    // Lignes financement bailleurs
    if (!empty($input['ligne_financement_bailleurs'])) {
        $ligneFinancementBailleurs = json_decode(str_replace("\\", "", $input['ligne_financement_bailleurs']), true);
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

    // Lignes financement co-financiers
    if (!empty($input['ligne_financement_cos'])) {
        $ligneFinancementCos = json_decode(str_replace("\\", "", $input['ligne_financement_cos']), true);
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
    $structure = $user->structures->first();
    $structure_id = $structure->id ?? null;

    $source = $structure && $structure->source_financements->isNotEmpty()
        ? $structure->source_financements->first()
        : null;
    $source_id = $source->id ?? null;

    $validator = Validator::make($input, ['annee' => 'nullable', 'monnaie' => 'nullable']);
    if ($validator->fails()) {
        return response()->json($validator->errors());
    }

    // Mise à jour des champs principaux
    $financement->update([
        'date_debut' => $input['date_debut'] ?? $financement->date_debut,
        'date_fin' => $input['date_fin'] ?? $financement->date_fin,
        'titre_projet' => $input['titre_projet'] ?? $financement->titre_projet,
        'objectif_global_projet' => $input['objectif_global_projet'] ?? $financement->objectif_global_projet,
        'montant_total_adaptation' => $input['montant_total_adaptation'] ?? $financement->montant_total_adaptation,
        'montant_total_attenuation' => $input['montant_total_attenuation'] ?? $financement->montant_total_attenuation,
        'montant_total_execute' => $input['montant_total_execute'] ?? $financement->montant_total_execute,
        'montant_total_restant' => $input['montant_total_restant'] ?? $financement->montant_total_restant,
        'renforcement_capacite' => $input['renforcement_capacite'] ?? $financement->renforcement_capacite,
        'transfert_technologie' => $input['transfert_technologie'] ?? $financement->transfert_technologie,
        'montant_total' => $input['montant_total'] ?? $financement->montant_total,
        'nombre_beneficiaire' => $input['nombre_beneficiaire'] ?? $financement->nombre_beneficiaire,
        'volume_co2' => $input['volume_co2'] ?? $financement->volume_co2
    ]);

    // Relations optionnelles
    $structure_id ? $financement->structure()->sync([$structure_id]) : $financement->structure()->detach();
    $source_id ? $financement->source()->sync([$source_id]) : $financement->source()->detach();
    !empty($input['annee']) ? $financement->annee()->sync([$input['annee']]) : null;
    !empty($input['monnaie']) ? $financement->monnaie()->sync([$input['monnaie']]) : null;
    !empty($input['dimension']) ? $financement->dimension()->sync([$input['dimension']]) : null;
    !empty($input['region']) ? $financement->region()->sync([$input['region']]) : null;

    // Lignes Mode Financements
    if (!empty($input['libelleModeFinancements'])) {
        $libelleModeFinancements = explode(",", $input['libelleModeFinancements']);
        $montantModeFinancements = explode(",", $input['montantModeFinancements']);

        // Détache les anciennes
        $financement->mode_financements()->detach();
        foreach ($libelleModeFinancements as $index => $libelle) {
            $ligneObj = ModeFinancement::create([
                'libelle' => $libelle,
                'montant' => $montantModeFinancements[$index] ?? 0,
                'status' => 'actif'
            ]);
            $financement->mode_financements()->attach($ligneObj);
        }
    }

    // Lignes Financements détaillées
    if (!empty($input['piliers'])) {
        $piliers = explode(",", $input['piliers']);
        $axes = explode(",", $input['axes']);
        $structure_sources = explode(",", $input['structure_sources']);
        $structure_beneficiaires = explode(",", $input['structure_beneficiaires']);
        $regions = explode(",", $input['regions']);

        $montantBienServicePrevus = explode(",", $input['montantBienServicePrevus']);
        $montantBienServiceMobilises = explode(",", $input['montantBienServiceMobilises']);
        $montantBienServiceExecutes = explode(",", $input['montantBienServiceExecutes']);
        $montantfinancementPrevus = explode(",", $input['montantfinancementPrevus']);
        $montantfinancementMobilises = explode(",", $input['montantfinancementMobilises']);
        $montantfinancementExecutes = explode(",", $input['montantfinancementExecutes']);

        // Détache les anciennes lignes
        $financement->ligne_financements()->detach();

        foreach ($piliers as $index => $pilier) {
            $structure_sourceObj = Structure::find($structure_sources[$index] ?? null);
            $structure_beneficiaireObj = Structure::find($structure_beneficiaires[$index] ?? null);
            $regionObj = Region::find($regions[$index] ?? null);
            $pilierObj = Pilier::find($pilier);
            $axeObj = Axe::find($axes[$index] ?? null);
            $anneeObj = Annee::find($input['annee']);
            $monnaieObj = Monnaie::find($input['monnaie']);
            $dimensionObj = Dimension::find($input['dimension']);
            $structureObj = Structure::find($structure_id);

            $ligneFinancementObj = LigneFinancement::create([
                'id_financement' => $financement->id,
                'id_structure' => $structure_id,
                'id_annee' => $input['annee'],
                'id_monnaie' => $input['monnaie'],
                'id_dimension' => $input['dimension'],
                'id_type_structure_source' => $structure_sourceObj->source_financements->first()->id ?? null,
                'id_structure_source' => $structure_sources[$index] ?? null,
                'id_structure_beneficiaire' => $structure_beneficiaires[$index] ?? null,
                'id_region' => $regions[$index] ?? null,
                'id_pilier' => $pilier,
                'id_axe' => $axes[$index] ?? null,
                'montantBienServicePrevus' => $montantBienServicePrevus[$index] ?? 0,
                'montantBienServiceMobilises' => $montantBienServiceMobilises[$index] ?? 0,
                'montantBienServiceExecutes' => $montantBienServiceExecutes[$index] ?? 0,
                'montantfinancementPrevus' => $montantfinancementPrevus[$index] ?? 0,
                'montantfinancementMobilises' => $montantfinancementMobilises[$index] ?? 0,
                'montantfinancementExecutes' => $montantfinancementExecutes[$index] ?? 0,
                'status' => $financement->status
            ]);

            // Attach relations
            $ligneFinancementObj->axe()->sync($axeObj ? [$axeObj->id] : []);
            $ligneFinancementObj->pilier()->sync($pilierObj ? [$pilierObj->id] : []);
            $ligneFinancementObj->structure_source()->sync($structure_sourceObj ? [$structure_sourceObj->id] : []);
            $ligneFinancementObj->structure_beneficiaire()->sync($structure_beneficiaireObj ? [$structure_beneficiaireObj->id] : []);
            $ligneFinancementObj->region()->sync($regionObj ? [$regionObj->id] : []);
            $ligneFinancementObj->financement()->sync([$financement->id]);
            $ligneFinancementObj->structure()->sync($structureObj ? [$structureObj->id] : []);
            $ligneFinancementObj->annee()->sync([$anneeObj->id]);
            $ligneFinancementObj->monnaie()->sync([$monnaieObj->id]);
            $ligneFinancementObj->dimension()->sync([$dimensionObj->id]);
        }
    }

    // Fichiers
    if (!empty($input['libelle_fichiers']) && !empty($input['input_fichiers'])) {
        $libelle_fichiers = $input['libelle_fichiers'];
        $input_fichiers = $input['input_fichiers'];

        // Détache les anciens fichiers
        $financement->fichiers()->detach();

        foreach ($libelle_fichiers as $index => $libelle) {
            if (isset($input_fichiers[$index]) && $input_fichiers[$index]->isValid()) {
                $file = $input_fichiers[$index];
                $upload_path = public_path('upload');
                $file_extension = $file->getClientOriginalExtension();
                $generated_new_name = 'accord_siege_' . time() . '_' . $index . '.' . $file_extension;
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
        "message" => "Financement enregistré avec succès.",
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
