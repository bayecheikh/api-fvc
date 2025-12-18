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
use App\Models\Financement;
use App\Models\User;
use App\Models\Fichier;
use App\Models\Structure;
use App\Models\Annee;
use App\Models\Monnaie;
use App\Models\LigneFinancement;
use App\Models\ModeFinancement;
use App\Models\LigneModeInvestissement;
use App\Models\Dimension;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Pilier;
use App\Models\Axe;

class RechercheInvestissementController extends Controller
{
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recherche(Request $request)
    {
        $input = $request->all();

        // Récupérer les paramètres avec des valeurs par défaut sécurisées
        $annees = isset($input['annees']) && !empty($input['annees']) ? explode(",", $input['annees']) : [];
        $monnaies = isset($input['monnaies']) && !empty($input['monnaies']) ? explode(",", $input['monnaies']) : [];
        $dimensions = isset($input['dimensions']) && !empty($input['dimensions']) ? explode(",", $input['dimensions']) : [];
        $type_structure_sources = isset($input['type_structure_sources']) && !empty($input['type_structure_sources']) ? explode(",", $input['type_structure_sources']) : [];
        $structure_sources = isset($input['structure_sources']) && !empty($input['structure_sources']) ? explode(",", $input['structure_sources']) : [];
        $structure_beneficiaires = isset($input['structure_beneficiaires']) && !empty($input['structure_beneficiaires']) ? explode(",", $input['structure_beneficiaires']) : [];
        $regions = isset($input['regions']) && !empty($input['regions']) ? explode(",", $input['regions']) : [];
        $piliers = isset($input['piliers']) && !empty($input['piliers']) ? explode(",", $input['piliers']) : [];
        $axes = isset($input['axes']) && !empty($input['axes']) ? explode(",", $input['axes']) : [];
        $structure_enregistrements = isset($input['structure_enregistrements']) && !empty($input['structure_enregistrements']) ? explode(",", $input['structure_enregistrements']) : [];

        // Nouveaux paramètres basés sur le modèle Financement
        $domaine_financements = isset($input['domaine_financements']) && !empty($input['domaine_financements']) ? explode(",", $input['domaine_financements']) : [];
        $source_financements = isset($input['source_financements']) && !empty($input['source_financements']) ? explode(",", $input['source_financements']) : [];
        $objectif_adaptations = isset($input['objectif_adaptations']) && !empty($input['objectif_adaptations']) ? explode(",", $input['objectif_adaptations']) : [];
        $objectif_attenuations = isset($input['objectif_attenuations']) && !empty($input['objectif_attenuations']) ? explode(",", $input['objectif_attenuations']) : [];
        $objectif_transversals = isset($input['objectif_transversals']) && !empty($input['objectif_transversals']) ? explode(",", $input['objectif_transversals']) : [];
        $agence_acredites = isset($input['agence_acredites']) && !empty($input['agence_acredites']) ? explode(",", $input['agence_acredites']) : [];
        $secteurs = isset($input['secteurs']) && !empty($input['secteurs']) ? explode(",", $input['secteurs']) : [];
        $bailleurs = isset($input['bailleurs']) && !empty($input['bailleurs']) ? explode(",", $input['bailleurs']) : [];

        $validator = Validator::make($input, [
            'annees' => 'nullable',
            'monnaies' => 'nullable',
            'regions' => 'nullable',
            'dimensions' => 'nullable',
            'piliers' => 'nullable',
            'axes' => 'nullable',
            'structure_sources' => 'nullable',
            'type_structure_sources' => 'nullable',
            'structure_beneficiaires' => 'nullable',
            'structure_enregistrements' => 'nullable',
            'domaine_financements' => 'nullable',
            'source_financements' => 'nullable',
            'objectif_adaptations' => 'nullable',
            'objectif_attenuations' => 'nullable',
            'objectif_transversals' => 'nullable',
            'agence_acredites' => 'nullable',
            'secteurs' => 'nullable',
            'bailleurs' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            // Construction de la requête selon les permissions
            if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('directeur_eps')) {
                $financements = Financement::with([
                    'annee',
                    'domaine_financement',
                    'source_financement',
                    'objectif_adaptations',
                    'objectif_attenuations',
                    'objectif_transversals',
                    'agence_acredite',
                    'ligne_financement_bailleurs',
                    'ligne_financement_cos',
                    'ligne_financement_secteurs',
                    'ligne_financement_zones',
                    'resumes',
                    'tableau_budgets',
                    'structure'
                ]);
            } else {
                $structure_id = User::find($request->user()->id)->structures[0]->id;
                $financements = Financement::with([
                    'annee',
                    'domaine_financement',
                    'source_financement',
                    'objectif_adaptations',
                    'objectif_attenuations',
                    'objectif_transversals',
                    'agence_acredite',
                    'ligne_financement_bailleurs',
                    'ligne_financement_cos',
                    'ligne_financement_secteurs',
                    'ligne_financement_zones',
                    'resumes',
                    'tableau_budgets',
                    'structure'
                ])->whereHas('structure', function ($q) use ($structure_id) {
                    $q->where('id', $structure_id);
                });
            }

            // Application des filtres
            if (!empty($annees)) {
                $financements = $financements->whereHas('annee', function ($q) use ($annees) {
                    $q->whereIn('id', $annees);
                });
            }

            if (!empty($domaine_financements)) {
                $financements = $financements->whereHas('domaine_financement', function ($q) use ($domaine_financements) {
                    $q->whereIn('id', $domaine_financements);
                });
            }

            if (!empty($source_financements)) {
                $financements = $financements->whereHas('source_financement', function ($q) use ($source_financements) {
                    $q->whereIn('id', $source_financements);
                });
            }

            if (!empty($objectif_adaptations)) {
                $financements = $financements->whereHas('objectif_adaptations', function ($q) use ($objectif_adaptations) {
                    $q->whereIn('id', $objectif_adaptations);
                });
            }

            if (!empty($objectif_attenuations)) {
                $financements = $financements->whereHas('objectif_attenuations', function ($q) use ($objectif_attenuations) {
                    $q->whereIn('id', $objectif_attenuations);
                });
            }

            if (!empty($objectif_transversals)) {
                $financements = $financements->whereHas('objectif_transversals', function ($q) use ($objectif_transversals) {
                    $q->whereIn('id', $objectif_transversals);
                });
            }

            if (!empty($agence_acredites)) {
                $financements = $financements->whereHas('agence_acredite', function ($q) use ($agence_acredites) {
                    $q->whereIn('id', $agence_acredites);
                });
            }

            if (!empty($secteurs)) {
                $financements = $financements->whereHas('ligne_financement_secteurs', function ($q) use ($secteurs) {
                    $q->whereIn('id', $secteurs);
                });
            }

            if (!empty($bailleurs)) {
                $financements = $financements->whereHas('ligne_financement_bailleurs', function ($q) use ($bailleurs) {
                    $q->whereIn('id', $bailleurs);
                });
            }

            if (!empty($regions)) {
                $financements = $financements->whereHas('ligne_financement_zones', function ($q) use ($regions) {
                    $q->whereIn('id', $regions);
                });
            }

            // Filtre par structures (si applicable)
            if (!empty($structure_sources)) {
                $financements = $financements->whereHas('structure', function ($q) use ($structure_sources) {
                    $q->whereIn('id', $structure_sources);
                });
            }

            // Filtre par status (uniquement publiés)
            $financements = $financements->where('status', 'like', '%publie%');

            // Pagination
            $perPage = $request->input('per_page', 20);
            $financements = $financements->orderBy('created_at', 'DESC')->paginate($perPage);

            $total = $financements->total();

            return response()->json([
                "success" => true,
                "message" => "Liste des financements",
                "data" => $financements,
                "total" => $total
            ]);
        }
    }
}
