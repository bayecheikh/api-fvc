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
use App\Models\DomaineFinancement;
use App\Models\ObjectifAdaptation;
use App\Models\ObjectifAttenuation;
use App\Models\ObjectifTransversal;
use App\Models\AgenceAcredite;
use App\Models\LigneFinancementBailleur;
use App\Models\LigneFinancementSecteur;
use App\Models\LigneFinancementZone;
use App\Models\LigneFinancementCo;
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
    try {
        $input = $request->all();
        $user = $request->user();
        $structure_id = $user->structures[0]->id ?? null;

        // Debug: voir ce qui est reçu
        \Log::info('Données reçues:', [
            'objectif_adaptation' => $input['objectif_adaptation'] ?? 'non défini',
            'objectif_attenuation' => $input['objectif_attenuation'] ?? 'non défini',
            'objectif_transversal' => $input['objectif_transversal'] ?? 'non défini',
            'agence_acredite' => $input['agence_acredite'] ?? 'non défini'
        ]);

        $validator = Validator::make($input, [
            'annee' => 'required',
            'domaine_financement' => 'required',
            'source_financement' => 'required',
            'titre_projet' => 'required',
            'objectif_global_projet' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Création du financement selon le rôle
        $state = $user->hasRole('point_focal') ? 'INITIER_INVESTISSEMENT' :
                 ($user->hasRole('admin_structure') ? 'VALIDATION_ADMIN_STRUCTURE' : null);

        $financement = Financement::create([
            'date_debut' => $input['date_debut'] ?? null,
            'date_fin' => $input['date_fin'] ?? null,
            'titre_projet' => $input['titre_projet'] ?? null,
            'objectif_global_projet' => $input['objectif_global_projet'] ?? null,
            'montant_total_adaptation' => $input['montant_total_adaptation'] ?? 0,
            'montant_total_attenuation' => $input['montant_total_attenuation'] ?? 0,
            'montant_total_execute' => $input['montant_total_execute'] ?? 0,
            'montant_total_restant' => $input['montant_total_restant'] ?? 0,
            'renforcement_capacite' => isset($input['renforcement_capacite']) ? (bool)$input['renforcement_capacite'] : false,
            'transfert_technologie' => isset($input['transfert_technologie']) ? (bool)$input['transfert_technologie'] : false,
            'montant_total' => $input['montant_total'] ?? 0,
            'nombre_beneficiaire' => $input['nombre_beneficiaire'] ?? 0,
            'volume_co2' => $input['volume_co2'] ?? 0,
            'state' => $state,
            'status' => 'brouillon'
        ]);

        // Structure
        if ($structure_id) {
            $structureObj = Structure::find($structure_id);
            if ($structureObj) {
                $financement->structure()->attach($structureObj);
            }
        }

        // Année
        if (!empty($input['annee'])) {
            $anneeObj = Annee::find($input['annee']);
            if ($anneeObj) {
                $financement->annee()->attach($anneeObj);
            }
        }

        // Domaine de financement
        if (!empty($input['domaine_financement'])) {
            $domaineId = intval($input['domaine_financement']);
            $domaineObj = DomaineFinancement::find($domaineId);
            if ($domaineObj) {
                $financement->domaine_financement()->attach($domaineObj);
            }
        }

        // Source de financement
        if (!empty($input['source_financement'])) {
            $sourceId = intval($input['source_financement']);
            $sourceObj = SourceFinancement::find($sourceId);
            if ($sourceObj) {
                $financement->source_financement()->attach($sourceObj);
            }
        }

        // Objectif adaptation (tableau JSON)
        if (!empty($input['objectif_adaptation'])) {
            $objectifsAdaptation = json_decode($input['objectif_adaptation'], true);
            if (is_array($objectifsAdaptation) && !empty($objectifsAdaptation)) {
                foreach ($objectifsAdaptation as $objectifId) {
                    $objectifObj = ObjectifAdaptation::find($objectifId);
                    if ($objectifObj) {
                        $financement->objectif_adaptations()->attach($objectifObj);
                    }
                }
            }
        }

        // Objectif attenuation (tableau JSON)
        if (!empty($input['objectif_attenuation'])) {
            $objectifsAttenuation = json_decode($input['objectif_attenuation'], true);
            if (is_array($objectifsAttenuation) && !empty($objectifsAttenuation)) {
                foreach ($objectifsAttenuation as $objectifId) {
                    $objectifObj = ObjectifAttenuation::find($objectifId);
                    if ($objectifObj) {
                        $financement->objectif_attenuations()->attach($objectifObj);
                    }
                }
            }
        }

        // Objectif transversal (tableau JSON)
        if (!empty($input['objectif_transversal'])) {
            $objectifsTransversal = json_decode($input['objectif_transversal'], true);
            if (is_array($objectifsTransversal) && !empty($objectifsTransversal)) {
                foreach ($objectifsTransversal as $objectifId) {
                    $objectifObj = ObjectifTransversal::find($objectifId);
                    if ($objectifObj) {
                        $financement->objectif_transversals()->attach($objectifObj);
                    }
                }
            }
        }

        // Agence accréditée (ID unique)
        if (!empty($input['agence_acredite'])) {
            $agenceId = intval($input['agence_acredite']);
            $agenceObj = AgenceAcredite::find($agenceId);
            if ($agenceObj) {
                $financement->agence_acredite()->attach($agenceObj);
            }
        }

        // Lignes financement secteurs
        if (!empty($input['ligne_financement_secteurs'])) {
            $ligneFinancementSecteurs = json_decode($input['ligne_financement_secteurs'], true);
            if (is_array($ligneFinancementSecteurs) && !empty($ligneFinancementSecteurs)) {
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
            $ligneFinancementZones = json_decode($input['ligne_financement_zones'], true);
            if (is_array($ligneFinancementZones) && !empty($ligneFinancementZones)) {
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
            $ligneFinancementBailleurs = json_decode($input['ligne_financement_bailleurs'], true);
            \Log::info('Lignes bailleurs décodées:', $ligneFinancementBailleurs);

            if (is_array($ligneFinancementBailleurs) && !empty($ligneFinancementBailleurs)) {
                foreach ($ligneFinancementBailleurs as $ligne) {
                    $ligneObj = LigneFinancementBailleur::create([
                        'id_investissement' => $financement->id,
                        'id_bailleur' => $ligne['bailleur'] ?? null,
                        'id_instrument_financier' => $ligne['instrument_financier'] ?? null,
                        'montant_total' => $ligne['montant_total'] ?? 0,
                        'status' => $financement->status
                    ]);
                    \Log::info('Ligne bailleur créée:', $ligneObj->toArray());
                    $financement->ligne_financement_bailleurs()->attach($ligneObj);
                }
            }
        }

        // Lignes financement co-financeurs
        if (!empty($input['ligne_financement_cos'])) {
            $ligneFinancementCos = json_decode($input['ligne_financement_cos'], true);
            if (is_array($ligneFinancementCos) && !empty($ligneFinancementCos)) {
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

        // Gérer les fichiers (resumes)
        if ($request->hasFile('resumes')) {
            foreach ($request->file('resumes') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('resumes', 'public');
                    $fichier = Fichier::create([
                        'nom' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type' => $file->getMimeType(),
                        'taille' => $file->getSize()
                    ]);
                    $financement->resumes()->attach($fichier);
                }
            }
        }

        // Gérer les fichiers (tableau_budgets)
        if ($request->hasFile('tableau_budgets')) {
            foreach ($request->file('tableau_budgets') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('budgets', 'public');
                    $fichier = Fichier::create([
                        'nom' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type' => $file->getMimeType(),
                        'taille' => $file->getSize()
                    ]);
                    $financement->tableau_budgets()->attach($fichier);
                }
            }
        }

        return response()->json([
            "success" => true,
            "message" => "Financement ajouté avec succès.",
            "data" => $financement
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur lors de la création du financement:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            "success" => false,
            "message" => "Erreur lors de la création du financement: " . $e->getMessage()
        ], 500);
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
    try {
        $input = $request->all();
        $user = $request->user();
        $structure_id = $user->structures[0]->id ?? null;

        // Debug: voir ce qui est reçu
        \Log::info('Données reçues pour update:', [
            'objectif_adaptation' => $input['objectif_adaptation'] ?? 'non défini',
            'objectif_attenuation' => $input['objectif_attenuation'] ?? 'non défini',
            'objectif_transversal' => $input['objectif_transversal'] ?? 'non défini',
            'agence_acredite' => $input['agence_acredite'] ?? 'non défini',
            'ligne_financement_bailleurs' => isset($input['ligne_financement_bailleurs']) ? 'défini' : 'non défini'
        ]);

        $validator = Validator::make($input, [
            'annee' => 'required',
            'domaine_financement' => 'required',
            'source_financement' => 'required',
            'titre_projet' => 'required',
            'objectif_global_projet' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /* ============================
         |  UPDATE CHAMPS DIRECTS
         ============================ */
        $financement->update([
            'date_debut' => $input['date_debut'] ?? $financement->date_debut,
            'date_fin' => $input['date_fin'] ?? $financement->date_fin,
            'titre_projet' => $input['titre_projet'] ?? $financement->titre_projet,
            'objectif_global_projet' => $input['objectif_global_projet'] ?? $financement->objectif_global_projet,
            'montant_total_adaptation' => $input['montant_total_adaptation'] ?? $financement->montant_total_adaptation,
            'montant_total_attenuation' => $input['montant_total_attenuation'] ?? $financement->montant_total_attenuation,
            'montant_total_execute' => $input['montant_total_execute'] ?? $financement->montant_total_execute,
            'montant_total_restant' => $input['montant_total_restant'] ?? $financement->montant_total_restant,
            'renforcement_capacite' => isset($input['renforcement_capacite']) ? (bool)$input['renforcement_capacite'] : $financement->renforcement_capacite,
            'transfert_technologie' => isset($input['transfert_technologie']) ? (bool)$input['transfert_technologie'] : $financement->transfert_technologie,
            'montant_total' => $input['montant_total'] ?? $financement->montant_total,
            'nombre_beneficiaire' => $input['nombre_beneficiaire'] ?? $financement->nombre_beneficiaire,
            'volume_co2' => $input['volume_co2'] ?? $financement->volume_co2,
        ]);

        /* ============================
         |  STRUCTURE
         ============================ */
        if ($structure_id && method_exists($financement, 'structure')) {
            $structureObj = Structure::find($structure_id);
            if ($structureObj) {
                $financement->structure()->sync([$structureObj->id]);
            }
        }

        /* ============================
         |  ANNÉE
         ============================ */
        if (!empty($input['annee']) && method_exists($financement, 'annee')) {
            $anneeObj = Annee::find($input['annee']);
            if ($anneeObj) {
                $financement->annee()->sync([$anneeObj->id]);
            }
        }

        /* ============================
         |  DOMAINE DE FINANCEMENT
         ============================ */
        if (!empty($input['domaine_financement']) && method_exists($financement, 'domaine_financement')) {
            $domaineId = intval($input['domaine_financement']);
            $domaineObj = DomaineFinancement::find($domaineId);
            if ($domaineObj) {
                $financement->domaine_financement()->sync([$domaineObj->id]);
            }
        }

        /* ============================
         |  SOURCE DE FINANCEMENT
         ============================ */
        if (!empty($input['source_financement']) && method_exists($financement, 'source_financement')) {
            $sourceId = intval($input['source_financement']);
            $sourceObj = SourceFinancement::find($sourceId);
            if ($sourceObj) {
                $financement->source_financement()->sync([$sourceObj->id]);
            }
        }

        /* ============================
         |  OBJECTIF ADAPTATION (tableau JSON)
         ============================ */
        if (method_exists($financement, 'objectif_adaptations')) {
            $objectifAdaptationIds = [];
            if (!empty($input['objectif_adaptation'])) {
                $objectifsAdaptation = json_decode($input['objectif_adaptation'], true);
                if (is_array($objectifsAdaptation) && !empty($objectifsAdaptation)) {
                    foreach ($objectifsAdaptation as $objectifId) {
                        $objectifObj = ObjectifAdaptation::find($objectifId);
                        if ($objectifObj) {
                            $objectifAdaptationIds[] = $objectifObj->id;
                        }
                    }
                }
            }
            $financement->objectif_adaptations()->sync($objectifAdaptationIds);
        }

        /* ============================
         |  OBJECTIF ATTENUATION (tableau JSON)
         ============================ */
        if (method_exists($financement, 'objectif_attenuations')) {
            $objectifAttenuationIds = [];
            if (!empty($input['objectif_attenuation'])) {
                $objectifsAttenuation = json_decode($input['objectif_attenuation'], true);
                if (is_array($objectifsAttenuation) && !empty($objectifsAttenuation)) {
                    foreach ($objectifsAttenuation as $objectifId) {
                        $objectifObj = ObjectifAttenuation::find($objectifId);
                        if ($objectifObj) {
                            $objectifAttenuationIds[] = $objectifObj->id;
                        }
                    }
                }
            }
            $financement->objectif_attenuations()->sync($objectifAttenuationIds);
        }

        /* ============================
         |  OBJECTIF TRANSVERSAL (tableau JSON)
         ============================ */
        if (method_exists($financement, 'objectif_transversals')) {
            $objectifTransversalIds = [];
            if (!empty($input['objectif_transversal'])) {
                $objectifsTransversal = json_decode($input['objectif_transversal'], true);
                if (is_array($objectifsTransversal) && !empty($objectifsTransversal)) {
                    foreach ($objectifsTransversal as $objectifId) {
                        $objectifObj = ObjectifTransversal::find($objectifId);
                        if ($objectifObj) {
                            $objectifTransversalIds[] = $objectifObj->id;
                        }
                    }
                }
            }
            $financement->objectif_transversals()->sync($objectifTransversalIds);
        }

        /* ============================
         |  AGENCE ACCRÉDITÉE (ID unique)
         ============================ */
        if (!empty($input['agence_acredite']) && method_exists($financement, 'agence_acredite')) {
            $agenceId = intval($input['agence_acredite']);
            $agenceObj = AgenceAcredite::find($agenceId);
            if ($agenceObj) {
                $financement->agence_acredite()->sync([$agenceObj->id]);
            }
        }

        /* ============================
         |  LIGNES FINANCEMENT SECTEURS
         ============================ */
        if (!empty($input['ligne_financement_secteurs']) && method_exists($financement, 'ligne_financement_secteurs')) {
            // Supprimer les anciennes lignes
            $financement->ligne_financement_secteurs()->detach();

            // Créer les nouvelles lignes
            $ligneFinancementSecteurs = json_decode($input['ligne_financement_secteurs'], true);
            if (is_array($ligneFinancementSecteurs) && !empty($ligneFinancementSecteurs)) {
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

        /* ============================
         |  LIGNES FINANCEMENT ZONES
         ============================ */
        if (!empty($input['ligne_financement_zones']) && method_exists($financement, 'ligne_financement_zones')) {
            // Supprimer les anciennes lignes
            $financement->ligne_financement_zones()->detach();

            // Créer les nouvelles lignes
            $ligneFinancementZones = json_decode($input['ligne_financement_zones'], true);
            if (is_array($ligneFinancementZones) && !empty($ligneFinancementZones)) {
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

        /* ============================
         |  LIGNES FINANCEMENT BAILLEURS
         ============================ */
        if (!empty($input['ligne_financement_bailleurs']) && method_exists($financement, 'ligne_financement_bailleurs')) {
            // Supprimer les anciennes lignes
            $financement->ligne_financement_bailleurs()->detach();

            // Créer les nouvelles lignes
            $ligneFinancementBailleurs = json_decode($input['ligne_financement_bailleurs'], true);
            \Log::info('Lignes bailleurs décodées pour update:', $ligneFinancementBailleurs);

            if (is_array($ligneFinancementBailleurs) && !empty($ligneFinancementBailleurs)) {
                foreach ($ligneFinancementBailleurs as $ligne) {
                    $ligneObj = LigneFinancementBailleur::create([
                        'id_investissement' => $financement->id,
                        'id_bailleur' => $ligne['bailleur'] ?? null,
                        'id_instrument_financier' => $ligne['instrument_financier'] ?? null,
                        'montant_total' => $ligne['montant_total'] ?? 0,
                        'status' => $financement->status
                    ]);
                    \Log::info('Ligne bailleur créée pour update:', $ligneObj->toArray());
                    $financement->ligne_financement_bailleurs()->attach($ligneObj);
                }
            }
        }

        /* ============================
         |  LIGNES FINANCEMENT CO-FINANCEURS
         ============================ */
        if (!empty($input['ligne_financement_cos']) && method_exists($financement, 'ligne_financement_cos')) {
            // Supprimer les anciennes lignes
            $financement->ligne_financement_cos()->detach();

            // Créer les nouvelles lignes
            $ligneFinancementCos = json_decode($input['ligne_financement_cos'], true);
            if (is_array($ligneFinancementCos) && !empty($ligneFinancementCos)) {
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

        /* ============================
         |  GESTION DES FICHIERS (résumés)
         ============================ */
        if ($request->hasFile('resumes')) {
            // Supprimer les anciens fichiers résumés
            $financement->resumes()->detach();

            // Ajouter les nouveaux fichiers
            foreach ($request->file('resumes') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('resumes', 'public');
                    $fichier = Fichier::create([
                        'nom' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type' => $file->getMimeType(),
                        'taille' => $file->getSize()
                    ]);
                    $financement->resumes()->attach($fichier);
                }
            }
        }

        /* ============================
         |  GESTION DES FICHIERS (tableaux budgétaires)
         ============================ */
        if ($request->hasFile('tableau_budgets')) {
            // Supprimer les anciens fichiers de budget
            $financement->tableau_budgets()->detach();

            // Ajouter les nouveaux fichiers
            foreach ($request->file('tableau_budgets') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('budgets', 'public');
                    $fichier = Fichier::create([
                        'nom' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type' => $file->getMimeType(),
                        'taille' => $file->getSize()
                    ]);
                    $financement->tableau_budgets()->attach($fichier);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Financement mis à jour avec succès',
            'data' => $financement->fresh()
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur lors de la mise à jour du financement:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            "success" => false,
            "message" => "Erreur lors de la mise à jour du financement: " . $e->getMessage()
        ], 500);
    }
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
            /* $financement->state = 'VALIDATION_ADMIN_STRUCTURE';
            $financement->status = 'a_valider'; */
            $financement->state = 'FIN_PROCESS';
            $financement->status = 'publie';
        }
        if ($request->user()->hasRole('admin_structure')){
            $financement->state = 'FIN_PROCESS';
            $financement->status = 'publie';
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
