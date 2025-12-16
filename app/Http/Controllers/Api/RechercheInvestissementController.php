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

    // Liste de tous les paramètres possibles
    $parameters = [
        'annees',
        'domaine_financements',
        'source_financements',
        'objectif_adaptations',
        'objectif_attenuations',
        'objectif_transversals',
        'agence_acredites',
        'secteurs',
        'bailleurs',
        'regions',
        'monnaies',
        'dimensions',
        'type_structure_sources',
        'structure_sources',
        'structure_beneficiaires',
        'piliers',
        'axes',
        'structure_enregistrements'
    ];

    // Préparer un tableau pour stocker les valeurs normalisées
    $normalized = [];

    foreach ($parameters as $param) {
        if ($request->has($param)) {
            $value = $request->input($param);

            if (is_array($value)) {
                // C'est déjà un tableau, filtrer les valeurs vides
                $filteredValues = array_filter($value, function($v) {
                    return $v !== '' && $v !== null && $v !== false;
                });
                // Réindexer le tableau
                $normalized[$param] = array_values($filteredValues);
            } elseif (is_string($value) && !empty(trim($value))) {
                // C'est une chaîne, vérifier si c'est une liste séparée par des virgules
                if (strpos($value, ',') !== false) {
                    // Séparer par virgules et filtrer
                    $values = explode(',', $value);
                    $filteredValues = array_filter($values, function($v) {
                        return trim($v) !== '' && trim($v) !== null;
                    });
                    $normalized[$param] = array_map('trim', $filteredValues);
                } else {
                    // C'est une valeur unique
                    $normalized[$param] = [trim($value)];
                }
            } else {
                // Vide ou autre type
                $normalized[$param] = [];
            }
        } else {
            $normalized[$param] = [];
        }
    }

    // Debug: logger les paramètres reçus
    \Log::info('Recherche financements - Paramètres reçus:', [
        'raw_input' => $input,
        'normalized' => $normalized
    ]);

    // Validation
    $validator = Validator::make($input, [
        'annees' => 'nullable',
        'domaine_financements' => 'nullable',
        'source_financements' => 'nullable',
        'objectif_adaptations' => 'nullable',
        'objectif_attenuations' => 'nullable',
        'objectif_transversals' => 'nullable',
        'agence_acredites' => 'nullable',
        'secteurs' => 'nullable',
        'bailleurs' => 'nullable',
        'regions' => 'nullable',
        'monnaies' => 'nullable',
        'dimensions' => 'nullable',
        'type_structure_sources' => 'nullable',
        'structure_sources' => 'nullable',
        'structure_beneficiaires' => 'nullable',
        'piliers' => 'nullable',
        'axes' => 'nullable',
        'structure_enregistrements' => 'nullable'
    ]);

    if ($validator->fails()) {
        return response()->json([
            "success" => false,
            "message" => "Erreur de validation",
            "errors" => $validator->errors()
        ], 400);
    }

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
        ])->whereHas('structure', function($q) use ($structure_id) {
            $q->where('id', $structure_id);
        });
    }

    // Appliquer les filtres avec les valeurs normalisées
    if (!empty($normalized['annees'])) {
        $financements = $financements->whereHas('annee', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['annees']);
        });
    }

    if (!empty($normalized['domaine_financements'])) {
        $financements = $financements->whereHas('domaine_financement', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['domaine_financements']);
        });
    }

    if (!empty($normalized['source_financements'])) {
        $financements = $financements->whereHas('source_financement', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['source_financements']);
        });
    }

    if (!empty($normalized['objectif_adaptations'])) {
        $financements = $financements->whereHas('objectif_adaptations', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['objectif_adaptations']);
        });
    }

    if (!empty($normalized['objectif_attenuations'])) {
        $financements = $financements->whereHas('objectif_attenuations', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['objectif_attenuations']);
        });
    }

    if (!empty($normalized['objectif_transversals'])) {
        $financements = $financements->whereHas('objectif_transversals', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['objectif_transversals']);
        });
    }

    if (!empty($normalized['agence_acredites'])) {
        $financements = $financements->whereHas('agence_acredite', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['agence_acredites']);
        });
    }

    if (!empty($normalized['secteurs'])) {
        $financements = $financements->whereHas('ligne_financement_secteurs', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['secteurs']);
        });
    }

    if (!empty($normalized['bailleurs'])) {
        $financements = $financements->whereHas('ligne_financement_bailleurs', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['bailleurs']);
        });
    }

    if (!empty($normalized['regions'])) {
        $financements = $financements->whereHas('ligne_financement_zones', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['regions']);
        });
    }

    if (!empty($normalized['structure_sources'])) {
        $financements = $financements->whereHas('structure', function($q) use ($normalized) {
            $q->whereIn('id', $normalized['structure_sources']);
        });
    }

    // Filtre par status (uniquement publiés)
    $financements = $financements->where('status', 'like', '%publie%');

    // Gestion de l'export vs recherche normale
    if ($request->has('export') && $request->input('export') === 'excel') {
        // Pour l'export, pas de pagination
        $financements = $financements->orderBy('created_at', 'DESC')->get();

        return response()->json([
            "success" => true,
            "message" => "Données pour export",
            "data" => $financements,
            "total" => $financements->count()
        ]);
    } else {
        // Pour l'affichage normal, avec pagination
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $financements = $financements->orderBy('created_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);

        $total = $financements->total();

        return response()->json([
            "success" => true,
            "message" => "Liste des financements",
            "data" => [
                "data" => $financements->items(),
                "current_page" => $financements->currentPage(),
                "last_page" => $financements->lastPage(),
                "per_page" => $financements->perPage(),
                "total" => $financements->total(),
                "from" => $financements->firstItem(),
                "to" => $financements->lastItem()
            ],
            "total" => $total
        ]);
    }
}
}
