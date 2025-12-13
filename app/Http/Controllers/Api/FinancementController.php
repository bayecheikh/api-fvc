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
    DB::beginTransaction();

    try {

        /* ============================
         * 1. VALIDATION
         * ============================ */
        $validator = Validator::make($request->all(), [
            'annee' => 'required|exists:annees,id',

            'date_debut' => 'nullable|date',
            'date_fin'   => 'nullable|date|after_or_equal:date_debut',

            'titre_projet'            => 'nullable|string',
            'objectif_global_projet'  => 'nullable|string',

            'montant_total_adaptation' => 'nullable|numeric',
            'montant_total_attenuation'=> 'nullable|numeric',
            'montant_total_execute'    => 'nullable|numeric',
            'montant_total_restant'    => 'nullable|numeric',
            'montant_total'            => 'nullable|numeric',

            'nombre_beneficiaire' => 'nullable|integer',
            'volume_co2'          => 'nullable|numeric',

            'renforcement_capacite' => 'nullable|boolean',
            'transfert_technologie' => 'nullable|boolean',

            'ligne_financement_secteurs' => 'nullable|string',
            'ligne_financement_zones'    => 'nullable|string',
            'ligne_financement_bailleurs'=> 'nullable|string',
            'ligne_financement_cos'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        /* ============================
         * 2. UTILISATEUR & STRUCTURE
         * ============================ */
        $user = $request->user();

        if (!$user || $user->structures->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur sans structure associée.'
            ], 403);
        }

        $structure = $user->structures->first();

        /* ============================
         * 3. ROLE & ETAT
         * ============================ */
        if ($user->hasRole('point_focal')) {
            $state = 'INITIER_INVESTISSEMENT';
        } elseif ($user->hasRole('admin_structure')) {
            $state = 'VALIDATION_ADMIN_STRUCTURE';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Rôle non autorisé.'
            ], 403);
        }

        /* ============================
         * 4. CREATION FINANCEMENT
         * ============================ */
        $financement = Financement::create([
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'titre_projet' => $request->titre_projet,
            'objectif_global_projet' => $request->objectif_global_projet,

            'montant_total_adaptation' => $request->montant_total_adaptation ?? 0,
            'montant_total_attenuation'=> $request->montant_total_attenuation ?? 0,
            'montant_total_execute'    => $request->montant_total_execute ?? 0,
            'montant_total_restant'    => $request->montant_total_restant ?? 0,
            'montant_total'            => $request->montant_total ?? 0,

            'nombre_beneficiaire' => $request->nombre_beneficiaire ?? 0,
            'volume_co2'          => $request->volume_co2 ?? 0,

            'renforcement_capacite' => (bool) $request->renforcement_capacite,
            'transfert_technologie' => (bool) $request->transfert_technologie,

            'state'  => $state,
            'status' => 'brouillon'
        ]);

        /* ============================
         * 5. RELATIONS SIMPLES
         * ============================ */
        $financement->structure()->sync([$structure->id]);
        $financement->annee()->sync([$request->annee]);

        /* ============================
         * 6. LIGNES – SECTEURS
         * ============================ */
        $this->handleJsonLines(
            $request->ligne_financement_secteurs,
            function ($line) use ($financement) {
                return LigneFinancementSecteur::create([
                    'id_investissement' => $financement->id,
                    'id_secteur'        => intval($line['secteur'] ?? 0),
                    'id_sous_secteur'   => intval($line['sous_secteur'] ?? 0),
                    'montant_total'     => $line['montant_total'] ?? 0,
                    'status'            => $financement->status
                ]);
            },
            fn ($obj) => $financement->ligne_financement_secteurs()->attach($obj->id)
        );

        /* ============================
         * 7. LIGNES – ZONES
         * ============================ */
        $this->handleJsonLines(
            $request->ligne_financement_zones,
            function ($line) use ($financement) {
                return LigneFinancementZone::create([
                    'id_investissement' => $financement->id,
                    'id_region'         => intval($line['region'] ?? 0),
                    'montant_total'     => $line['montant_total'] ?? 0,
                    'status'            => $financement->status
                ]);
            },
            fn ($obj) => $financement->ligne_financement_zones()->attach($obj->id)
        );

        /* ============================
         * 8. LIGNES – BAILLEURS
         * ============================ */
        $this->handleJsonLines(
            $request->ligne_financement_bailleurs,
            function ($line) use ($financement) {
                return LigneFinancementBailleur::create([
                    'id_investissement'       => $financement->id,
                    'id_bailleur'             => intval($line['bailleur'] ?? 0),
                    'id_instrumet_financier'  => intval($line['instrumet_financier'] ?? 0),
                    'montant_total'           => $line['montant_total'] ?? 0,
                    'status'                  => $financement->status
                ]);
            },
            fn ($obj) => $financement->ligne_financement_bailleurs()->attach($obj->id)
        );

        /* ============================
         * 9. LIGNES – CO-FINANCEMENT
         * ============================ */
        $this->handleJsonLines(
            $request->ligne_financement_cos,
            function ($line) use ($financement) {
                return LigneFinancementCo::create([
                    'id_investissement'      => $financement->id,
                    'id_instrument_financier'=> intval($line['instrument_financier'] ?? 0),
                    'nom_co_financier'       => $line['nom_co_financier'] ?? '',
                    'montant_co_financier'   => $line['montant_co_financier'] ?? 0,
                    'status'                 => $financement->status
                ]);
            },
            fn ($obj) => $financement->ligne_financement_cos()->attach($obj->id)
        );

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Financement ajouté avec succès.',
            'data'    => $financement->id
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l’enregistrement du financement.',
            'error'   => $e->getMessage()
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
    DB::beginTransaction();

    try {
        $validator = Validator::make($request->all(), [
            'annee' => 'nullable|exists:annees,id',
            'monnaie' => 'nullable|exists:monnaies,id',
            'ligneModeFinancements' => 'nullable|string',
            'ligneFinancements' => 'nullable|string',
            'libelle_fichiers' => 'nullable|array',
            'input_fichiers' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $structure_id = $user->structures->first()->id ?? null;
        $source_id = $user->structures->first()->source_financements->first()->id ?? null;

        if (!$structure_id || !$source_id) {
            throw new \Exception("Utilisateur sans structure ou source.");
        }

        /* ============================
         * 1. RELATIONS SIMPLES
         * ============================ */
        $financement->structure()->sync([$structure_id]);
        $financement->source()->sync([$source_id]);
        $financement->annee()->sync([$request->annee]);
        $financement->monnaie()->sync([$request->monnaie]);

        if(!empty($request->region)) $financement->region()->sync([$request->region]);
        if(!empty($request->dimension)) $financement->dimension()->sync([$request->dimension]);

        /* ============================
         * 2. MODE FINANCEMENTS
         * ============================ */
        $libelles = $request->libelleModeFinancements ?? [];
        $montants = $request->montantModeFinancements ?? [];

        if(!empty($libelles)){
            $financement->mode_financements()->detach();

            foreach($libelles as $i => $libelle){
                $obj = ModeFinancement::create([
                    'libelle' => $libelle,
                    'montant' => $montants[$i] ?? 0,
                    'status'  => 'actif'
                ]);
                $financement->mode_financements()->attach($obj->id);
            }
        }

        /* ============================
         * 3. LIGNES FINANCEMENTS (JSON)
         * ============================ */
        $this->handleJsonLines(
            $request->ligneFinancements,
            function($line) use ($financement, $request) {
                $structure_id = $request->user()->structures->first()->id;
                $annee_id = $request->annee;
                $monnaie_id = $request->monnaie;
                $dimension_id = $request->dimension;

                return LigneFinancement::create([
                    'id_financement' => $financement->id,
                    'id_structure' => $structure_id,
                    'id_annee' => $annee_id,
                    'id_monnaie' => $monnaie_id,
                    'id_dimension' => $dimension_id,
                    'id_structure_source' => intval($line['structure_source'] ?? 0),
                    'id_structure_beneficiaire' => intval($line['structure_beneficiaire'] ?? 0),
                    'id_region' => intval($line['region'] ?? 0),
                    'id_pilier' => intval($line['pilier'] ?? 0),
                    'id_axe' => intval($line['axe'] ?? 0),
                    'montantBienServicePrevus' => $line['montantBienServicePrevus'] ?? 0,
                    'montantBienServiceMobilises' => $line['montantBienServiceMobilises'] ?? 0,
                    'montantBienServiceExecutes' => $line['montantBienServiceExecutes'] ?? 0,
                    'montantfinancementPrevus' => $line['montantfinancementPrevus'] ?? 0,
                    'montantfinancementMobilises' => $line['montantfinancementMobilises'] ?? 0,
                    'montantfinancementExecutes' => $line['montantfinancementExecutes'] ?? 0,
                    'status' => $financement->status
                ]);
            },
            fn($obj) => $financement->ligne_financements()->attach($obj->id)
        );

        /* ============================
         * 4. FICHIERS
         * ============================ */
        if(!empty($request->libelle_fichiers) && !empty($request->input_fichiers)){
            $financement->fichiers()->detach();

            foreach($request->libelle_fichiers as $i => $libelle){
                $file = $request->input_fichiers[$i] ?? null;
                if($file && $file->isValid()){
                    $generated_new_name = 'accord_siege_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload'), $generated_new_name);

                    $fichier = Fichier::create([
                        'name' => $libelle,
                        'url' => 'upload/' . $generated_new_name,
                        'extension' => $file->getClientOriginalExtension(),
                        'description' => 'Fichier'
                    ]);
                    $financement->fichiers()->attach($fichier->id);
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Financement mis à jour avec succès.'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour.',
            'error' => $e->getMessage()
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
