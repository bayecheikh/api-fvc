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

    // Normalisation des paramètres
    $normalized = [];

    foreach ($parameters as $param) {
        if ($request->has($param)) {
            $value = $request->input($param);

            if (is_array($value)) {
                $filtered = array_filter($value, fn ($v) => $v !== '' && $v !== null && $v !== false);
                $normalized[$param] = array_values($filtered);
            } elseif (is_string($value) && trim($value) !== '') {
                if (strpos($value, ',') !== false) {
                    $values = explode(',', $value);
                    $filtered = array_filter($values, fn ($v) => trim($v) !== '');
                    $normalized[$param] = array_map('trim', $filtered);
                } else {
                    $normalized[$param] = [trim($value)];
                }
            } else {
                $normalized[$param] = [];
            }
        } else {
            $normalized[$param] = [];
        }
    }

    \Log::info('Recherche financements - Paramètres normalisés', $normalized);

    // Validation
    $validator = Validator::make($input, array_fill_keys($parameters, 'nullable'));

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors'  => $validator->errors()
        ], 400);
    }

    /**
     * ================================
     * CONSTRUCTION DE LA REQUÊTE
     * ================================
     */

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
    ])
    ->whereRaw('LOWER(status) = ?', ['publie']);

    /**
     * ================================
     * FILTRAGE PAR STRUCTURE UTILISATEUR
     * ================================
     * - si l'utilisateur a une structure :
     *   -> financements sans structure
     *   -> OU financements de sa structure
     */

    $user = $request->user();

    if (
        !$user->hasRole('super_admin') &&
        !$user->hasRole('directeur_eps') &&
        $user->structures->count() > 0
    ) {
        $structureId = $user->structures->first()->id;

        $financements->where(function ($q) use ($structureId) {
            $q->whereNull('structure_id')
              ->orWhere('structure_id', $structureId);
        });
    }

    /**
     * ================================
     * FILTRES MÉTIERS (OPTIONNELS)
     * ================================
     */

    if (!empty($normalized['annees'])) {
        $financements->whereHas('annee', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['annees']);
        });
    }

    if (!empty($normalized['domaine_financements'])) {
        $financements->whereHas('domaine_financement', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['domaine_financements']);
        });
    }

    if (!empty($normalized['source_financements'])) {
        $financements->whereHas('source_financement', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['source_financements']);
        });
    }

    if (!empty($normalized['objectif_adaptations'])) {
        $financements->whereHas('objectif_adaptations', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['objectif_adaptations']);
        });
    }

    if (!empty($normalized['objectif_attenuations'])) {
        $financements->whereHas('objectif_attenuations', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['objectif_attenuations']);
        });
    }

    if (!empty($normalized['objectif_transversals'])) {
        $financements->whereHas('objectif_transversals', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['objectif_transversals']);
        });
    }

    if (!empty($normalized['agence_acredites'])) {
        $financements->whereHas('agence_acredite', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['agence_acredites']);
        });
    }

    if (!empty($normalized['secteurs'])) {
        $financements->whereHas('ligne_financement_secteurs', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['secteurs']);
        });
    }

    if (!empty($normalized['bailleurs'])) {
        $financements->whereHas('ligne_financement_bailleurs', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['bailleurs']);
        });
    }

    if (!empty($normalized['regions'])) {
        $financements->whereHas('ligne_financement_zones', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['regions']);
        });
    }

    if (!empty($normalized['structure_sources'])) {
        $financements->whereHas('structure', function ($q) use ($normalized) {
            $q->whereIn('id', $normalized['structure_sources']);
        });
    }

    /**
     * ================================
     * EXPORT OU PAGINATION
     * ================================
     */

    if ($request->input('export') === 'excel') {
        $data = $financements->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'success' => true,
            'message' => 'Données pour export',
            'data'    => $data,
            'total'   => $data->count()
        ]);
    }

    $perPage = (int) $request->input('per_page', 20);
    $page    = (int) $request->input('page', 1);

    $paginated = $financements
        ->orderBy('created_at', 'DESC')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'success' => true,
        'message' => 'Liste des financements',
        'data' => [
            'data'         => $paginated->items(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
            'from'         => $paginated->firstItem(),
            'to'           => $paginated->lastItem()
        ],
        'total' => $paginated->total()
    ]);
}

}
