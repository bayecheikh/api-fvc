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
use App\Models\LigneFinancementSecteur;
use App\Models\LigneFinancementBailleur;
use App\Models\ModeFinancement;
use App\Models\LigneModeInvestissement;
use App\Models\Dimension;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Secteur;
use App\Models\Pilier;
use App\Models\Axe;
use App\Models\DomaineFinancement;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
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
        return '';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allPilier()
    {
        $piliers = Pilier::with('axes')->get();
        return response()->json(["success" => true, "message" => "Liste des piliers", "data" => $piliers]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allSecteur()
    {
        $secteurs = Secteur::with('sous_secteurs')->get();
        return response()->json(["success" => true, "message" => "Liste des secteurs", "data" => $secteurs]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function investissementByPilier($idPilier)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('piliers', function ($q) use ($idPilier) {
                $q->where('id', $idPilier);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par pilier", "data" => $investissements, "total" => $total]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function investissementBySecteur($idSecteur)
    {
        $investissements = LigneFinancementSecteur::where('id_secteur', $idSecteur)->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par pilier", "data" => $investissements, "total" => $total]);
    }
    public function investissementByAxe($idAxe)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('axes', function ($q) use ($idAxe) {
                $q->where('id', $idAxe);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par axe", "data" => $investissements, "total" => $total]);
    }
    public function investissementByAnnee($idAnnee)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('annee', function ($q) use ($idAnnee) {
                $q->where('id', $idAnnee);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par annee", "data" => $investissements, "total" => $total]);
    }
    public function investissementByRegion($idRegion)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('region', function ($q) use ($idRegion) {
                $q->where('id', $idRegion);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par region", "data" => $investissements, "total" => $total]);
    }
    public function investissementByMonnaie($idMonnaie)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('monnaie', function ($q) use ($idMonnaie) {
                $q->where('id', $idMonnaie);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par monnaie", "data" => $investissements, "total" => $total]);
    }
    public function investissementByStructure($idStructure)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('structure', function ($q) use ($idStructure) {
                $q->where('id', $idStructure);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par structure", "data" => $investissements, "total" => $total]);
    }
    public function investissementByDimension($idDimension)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('dimension', function ($q) use ($idDimension) {
                $q->where('id', $idDimension);
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par dimension", "data" => $investissements, "total" => $total]);
    }
    public function investissementBySource($idSource)
    {
        $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('dimension')
            ->with('piliers')
            ->with('axes')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('fichiers')

            ->whereHas('structure', function ($q) use ($idSource) {
                $q->whereHas('source_financements', function ($q) use ($idSource) {
                    $q->where('id', $idSource);
                });
            })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par structure", "data" => $investissements, "total" => $total]);
    }

    public function allStats()
    {
        $status = 'publie';
        $investissements = LigneFinancement::with('investissement')
            ->with('pilier')
            ->with('axe')
            ->with('structure_source')
            ->with('type_structure_source')
            ->with('structure_beneficiaire')
            ->with('region')
            ->with('structure')
            ->with('annee')
            ->with('monnaie')
            ->with('dimension')
            ->whereHas('investissement', function ($q) use ($status) {
                $q->where('status', 'like', '%publie%');
            })->paginate(0);
        $investissements->load('investissement.mode_financements');

        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des lignes financements", "data" => $investissements, "total" => $total]);
    }

    //************************KPI******************//


    //NEW KPI
    public function getKpiParInstrument(Request $request)
    {
        try {
            $query = DB::table('instrument_financiers')
                ->select(
                    'instrument_financiers.id',
                    'instrument_financiers.libelle as instrument',
                    DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
                    DB::raw('COALESCE(SUM(CAST(ligne_financement_bailleurs.montant_total AS DECIMAL(15,2))), 0) as montant_total'),
                    DB::raw('COALESCE(AVG(CAST(ligne_financement_bailleurs.montant_total AS DECIMAL(15,2))), 0) as montant_moyen')
                )
                ->leftJoin(
                    'ligne_financement_bailleurs',
                    'instrument_financiers.id',
                    '=',
                    'ligne_financement_bailleurs.id_instrument_financier'
                )
                ->leftJoin(
                    'ligne_fine_bailleurs_fines',
                    'ligne_financement_bailleurs.id',
                    '=',
                    'ligne_fine_bailleurs_fines.ligne_financement_bailleur_id'
                )
                ->leftJoin(
                    'financements',
                    'ligne_fine_bailleurs_fines.financement_id',
                    '=',
                    'financements.id'
                )
                ->leftJoin(
                    'annees_fines',
                    'financements.id',
                    '=',
                    'annees_fines.financement_id'
                )
                ->leftJoin(
                    'annees',
                    'annees_fines.annee_id',
                    '=',
                    'annees.id'
                )
                ->where('financements.status', '!=', 'brouillon')
                ->whereNotNull('instrument_financiers.libelle');

            // Appliquer les filtres
            if ($request->has('annee_id') && $request->annee_id) {
                $query->where('annees.id', $request->annee_id);
            }

            if ($request->has('date_debut') && $request->date_debut) {
                $query->where('financements.date_debut', '>=', $request->date_debut);
            }

            if ($request->has('date_fin') && $request->date_fin) {
                $query->where('financements.date_fin', '<=', $request->date_fin);
            }

            if ($request->has('domaine_id') && $request->domaine_id) {
                $query->leftJoin(
                    'domaine_fines_fines',
                    'financements.id',
                    '=',
                    'domaine_fines_fines.financement_id'
                )
                    ->where('domaine_fines_fines.domaine_financement_id', $request->domaine_id);
            }

            if ($request->has('status') && $request->status) {
                $query->where('financements.status', $request->status);
            }

            $statistiques = $query->groupBy('instrument_financiers.id', 'instrument_financiers.libelle')
                ->orderBy('montant_total', 'DESC')
                ->get();

            $totalMontant = $statistiques->sum('montant_total');

            $resultat = $statistiques->map(function ($item) use ($totalMontant) {
                $pourcentage = $totalMontant > 0 ? round(($item->montant_total / $totalMontant) * 100, 2) : 0;

                return [
                    'id' => $item->id,
                    'instrument' => $item->instrument,
                    'nombre_financements' => (int)$item->nombre_financements,
                    'montant_total' => (float)$item->montant_total,
                    'montant_moyen' => (float)$item->montant_moyen,
                    'pourcentage_total' => $pourcentage
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultat,
                'total_montant' => $totalMontant,
                'total_projets' => $statistiques->sum('nombre_financements'),
                'message' => 'KPI par instrument financier récupéré avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du KPI par instrument: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getKpiParDomaine(Request $request)
    {
        try {
            $query = DB::table('domaine_financements')
                ->select(
                    'domaine_financements.id',
                    'domaine_financements.libelle as domaine',
                    DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
                    DB::raw('COALESCE(SUM(CAST(financements.montant_total AS DECIMAL(15,2))), 0) as montant_total'),
                    DB::raw('COALESCE(AVG(CAST(financements.montant_total AS DECIMAL(15,2))), 0) as montant_moyen')
                )
                ->leftJoin(
                    'domaine_fines_fines',
                    'domaine_financements.id',
                    '=',
                    'domaine_fines_fines.domaine_financement_id'
                )
                ->leftJoin(
                    'financements',
                    'domaine_fines_fines.financement_id',
                    '=',
                    'financements.id'
                )
                ->leftJoin(
                    'annees_fines',
                    'financements.id',
                    '=',
                    'annees_fines.financement_id'
                )
                ->leftJoin(
                    'annees',
                    'annees_fines.annee_id',
                    '=',
                    'annees.id'
                )
                ->where('financements.status', '!=', 'brouillon')
                ->whereNotNull('domaine_financements.libelle');

            // Appliquer les filtres
            if ($request->has('annee_id') && $request->annee_id) {
                $query->where('annees.id', $request->annee_id);
            }

            if ($request->has('date_debut') && $request->date_debut) {
                $query->where('financements.date_debut', '>=', $request->date_debut);
            }

            if ($request->has('date_fin') && $request->date_fin) {
                $query->where('financements.date_fin', '<=', $request->date_fin);
            }

            if ($request->has('instrument_id') && $request->instrument_id) {
                $query->leftJoin(
                    'ligne_fine_bailleurs_fines',
                    'financements.id',
                    '=',
                    'ligne_fine_bailleurs_fines.financement_id'
                )
                    ->leftJoin(
                        'ligne_financement_bailleurs',
                        'ligne_fine_bailleurs_fines.ligne_financement_bailleur_id',
                        '=',
                        'ligne_financement_bailleurs.id'
                    )
                    ->where('ligne_financement_bailleurs.id_instrument_financier', $request->instrument_id);
            }

            if ($request->has('status') && $request->status) {
                $query->where('financements.status', $request->status);
            }

            $statistiques = $query->groupBy('domaine_financements.id', 'domaine_financements.libelle')
                ->orderBy('montant_total', 'DESC')
                ->get();

            $totalMontant = $statistiques->sum('montant_total');

            $resultat = $statistiques->map(function ($item) use ($totalMontant) {
                $pourcentage = $totalMontant > 0 ? round(($item->montant_total / $totalMontant) * 100, 2) : 0;

                return [
                    'id' => $item->id,
                    'domaine' => $item->domaine,
                    'nombre_financements' => (int)$item->nombre_financements,
                    'montant_total' => (float)$item->montant_total,
                    'montant_moyen' => (float)$item->montant_moyen,
                    'pourcentage_total' => $pourcentage
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultat,
                'total_montant' => $totalMontant,
                'total_projets' => $statistiques->sum('nombre_financements'),
                'message' => 'KPI par domaine de financement récupéré avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du KPI par domaine: ' . $e->getMessage()
            ], 500);
        }
    }

public function getKpiParSecteur(Request $request)
{
    try {
        $query = DB::table('secteurs')
            ->select(
                'secteurs.id',
                'secteurs.libelle as secteur',
                DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
                DB::raw('COALESCE(SUM(CAST(ligne_financement_secteurs.montant_total AS DECIMAL(15,2))), 0) as montant_total'),
                DB::raw('COALESCE(AVG(CAST(ligne_financement_secteurs.montant_total AS DECIMAL(15,2))), 0) as montant_moyen')
            )

            /**
             * ================================
             * JOINTURES
             * ================================
             */

            ->leftJoin('ligne_financement_secteurs', function ($join) {
                $join->on('secteurs.id', '=', 'ligne_financement_secteurs.id_secteur')
                     ->where('ligne_financement_secteurs.montant_total', '>', 0);
            })

            ->leftJoin(
                'ligne_fine_secteurs_fines',
                'ligne_financement_secteurs.id',
                '=',
                'ligne_fine_secteurs_fines.ligne_financement_secteur_id'
            )

            /**
             * 🔴 JOINTURE CLÉ
             * ➜ INNER JOIN pour exclure les secteurs sans financement réel
             */
            ->join(
                'financements',
                'ligne_fine_secteurs_fines.financement_id',
                '=',
                'financements.id'
            )

            ->leftJoin(
                'annees_fines',
                'financements.id',
                '=',
                'annees_fines.financement_id'
            )

            ->leftJoin(
                'annees',
                'annees_fines.annee_id',
                '=',
                'annees.id'
            )

            /**
             * ================================
             * CONTRAINTES GLOBALES
             * ================================
             */

            ->where('financements.status', '!=', 'brouillon')
            ->whereNotNull('secteurs.libelle');

        /**
         * ================================
         * FILTRES OPTIONNELS
         * ================================
         */

        if ($request->filled('annee_id')) {
            $query->where('annees.id', $request->annee_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('financements.date_debut', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('financements.date_fin', '<=', $request->date_fin);
        }

        /**
         * ================================
         * AGRÉGATION
         * ================================
         */

        $statistiques = $query
            ->groupBy('secteurs.id', 'secteurs.libelle')
            ->orderBy('montant_total', 'DESC')
            ->get();

        /**
         * ================================
         * AJOUT DES SOUS-SECTEURS
         * ================================
         */

        $resultat = $statistiques->map(function ($secteur) use ($request) {
            $sousSecteurs = $this->getSousSecteursParSecteur($secteur->id, $request);

            return [
                'id' => $secteur->id,
                'secteur' => $secteur->secteur,
                'nombre_financements' => (int) $secteur->nombre_financements,
                'montant_total' => (float) $secteur->montant_total,
                'montant_moyen' => (float) $secteur->montant_moyen,
                'sous_secteurs' => $sousSecteurs
            ];
        });

        /**
         * ================================
         * POURCENTAGES
         * ================================
         */

        $totalMontant = $statistiques->sum('montant_total');

        $resultatAvecPourcentage = $resultat->map(function ($item) use ($totalMontant) {
            $item['pourcentage_total'] = $totalMontant > 0
                ? round(($item['montant_total'] / $totalMontant) * 100, 2)
                : 0;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $resultatAvecPourcentage,
            'total_montant' => $totalMontant,
            'total_projets' => $statistiques->sum('nombre_financements'),
            'nombre_secteurs' => $statistiques->count(),
            'message' => 'KPI par secteur récupéré avec succès'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération du KPI par secteur : ' . $e->getMessage()
        ], 500);
    }
}


private function getSousSecteursParSecteur($secteurId, $request)
{
    $query = DB::table('sous_secteurs')
        ->select(
            'sous_secteurs.id',
            'sous_secteurs.libelle as sous_secteur',
            DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
            DB::raw('COALESCE(SUM(CAST(ligne_financement_secteurs.montant_total AS DECIMAL(15,2))), 0) as montant_total')
        )
        ->leftJoin('ligne_financement_secteurs', function($join) use ($secteurId) {
            $join->on('sous_secteurs.id', '=', 'ligne_financement_secteurs.id_sous_secteur')
                 ->where('ligne_financement_secteurs.id_secteur', $secteurId)
                 ->where('ligne_financement_secteurs.montant_total', '>', 0);
        })
        ->leftJoin('ligne_fine_secteurs_fines',
            'ligne_financement_secteurs.id',
            '=',
            'ligne_fine_secteurs_fines.ligne_financement_secteur_id'
        )
        ->leftJoin('financements',
            'ligne_fine_secteurs_fines.financement_id',
            '=',
            'financements.id'
        )
        ->leftJoin('annees_fines',
            'financements.id',
            '=',
            'annees_fines.financement_id'
        )
        ->leftJoin('annees',
            'annees_fines.annee_id',
            '=',
            'annees.id'
        )
        ->where('financements.status', '!=', 'brouillon')
        ->whereNotNull('sous_secteurs.libelle');

    // Appliquer les mêmes filtres
    if ($request->has('annee_id') && $request->annee_id) {
        $query->where('annees.id', $request->annee_id);
    }

    if ($request->has('date_debut') && $request->date_debut) {
        $query->where('financements.date_debut', '>=', $request->date_debut);
    }

    if ($request->has('date_fin') && $request->date_fin) {
        $query->where('financements.date_fin', '<=', $request->date_fin);
    }

    return $query->groupBy('sous_secteurs.id', 'sous_secteurs.libelle')
        ->orderBy('montant_total', 'DESC')
        ->get();
}
public function getKpiParRegion(Request $request)
{
    try {
        $query = DB::table('regions')
            ->select(
                'regions.id',
                'regions.nom_region as region',
                'regions.latitude',
                'regions.longitude',
                DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
                DB::raw('COALESCE(SUM(CAST(ligne_financement_zones.montant_total AS DECIMAL(15,2))), 0) as montant_total'),
                DB::raw('COALESCE(AVG(CAST(ligne_financement_zones.montant_total AS DECIMAL(15,2))), 0) as montant_moyen')
            )

            /**
             * ================================
             * JOINTURES
             * ================================
             */

            ->leftJoin('ligne_financement_zones', function ($join) {
                $join->on('regions.id', '=', 'ligne_financement_zones.id_region')
                     ->where('ligne_financement_zones.montant_total', '>', 0);
            })

            ->leftJoin(
                'ligne_fine_zones_fines',
                'ligne_financement_zones.id',
                '=',
                'ligne_fine_zones_fines.ligne_financement_zone_id'
            )

            /**
             * 🔴 JOINTURE CLÉ
             * ➜ INNER JOIN pour exclure les régions sans financement réel
             */
            ->join(
                'financements',
                'ligne_fine_zones_fines.financement_id',
                '=',
                'financements.id'
            )

            ->leftJoin(
                'annees_fines',
                'financements.id',
                '=',
                'annees_fines.financement_id'
            )

            ->leftJoin(
                'annees',
                'annees_fines.annee_id',
                '=',
                'annees.id'
            )

            /**
             * ================================
             * CONTRAINTES GLOBALES
             * ================================
             */

            ->where('financements.status', '!=', 'brouillon')
            ->whereNotNull('regions.nom_region');

        /**
         * ================================
         * FILTRES OPTIONNELS
         * ================================
         */

        if ($request->filled('annee_id')) {
            $query->where('annees.id', $request->annee_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('financements.date_debut', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('financements.date_fin', '<=', $request->date_fin);
        }

        if ($request->filled('secteur_id')) {
            $query
                ->leftJoin(
                    'ligne_fine_secteurs_fines',
                    'financements.id',
                    '=',
                    'ligne_fine_secteurs_fines.financement_id'
                )
                ->leftJoin(
                    'ligne_financement_secteurs',
                    'ligne_fine_secteurs_fines.ligne_financement_secteur_id',
                    '=',
                    'ligne_financement_secteurs.id'
                )
                ->where('ligne_financement_secteurs.id_secteur', $request->secteur_id);
        }

        /**
         * ================================
         * AGRÉGATION
         * ================================
         */

        $statistiques = $query
            ->groupBy(
                'regions.id',
                'regions.nom_region',
                'regions.latitude',
                'regions.longitude'
            )
            ->orderBy('montant_total', 'DESC')
            ->get();

        /**
         * ================================
         * POST-TRAITEMENT
         * ================================
         */

        $totalMontant = $statistiques->sum('montant_total');

        $resultat = $statistiques->map(function ($item) use ($totalMontant) {
            $pourcentage = $totalMontant > 0
                ? round(($item->montant_total / $totalMontant) * 100, 2)
                : 0;

            return [
                'id' => $item->id,
                'region' => $item->region,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'nombre_financements' => (int) $item->nombre_financements,
                'montant_total' => (float) $item->montant_total,
                'montant_moyen' => (float) $item->montant_moyen,
                'pourcentage_total' => $pourcentage
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $resultat,
            'total_montant' => $totalMontant,
            'total_projets' => $statistiques->sum('nombre_financements'),
            'nombre_regions' => $statistiques->count(),
            'message' => 'KPI par région récupéré avec succès'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération du KPI par région : ' . $e->getMessage()
        ], 500);
    }
}



public function getKpiCombineInstrumentDomaine(Request $request)
{
    try {
        // Construire la requête principale avec sous-requêtes
        $query = DB::table('instrument_financiers')
            ->select([
                'instrument_financiers.id as instrument_id',
                'instrument_financiers.libelle as instrument',
                'domaine_financements.id as domaine_id',
                'domaine_financements.libelle as domaine',
                // Sous-requête pour compter les financements distincts
                DB::raw('(SELECT COUNT(DISTINCT f.id)
                          FROM financements f
                          INNER JOIN ligne_fine_bailleurs_fines lbf ON f.id = lbf.financement_id
                          INNER JOIN ligne_financement_bailleurs lfb ON lbf.ligne_financement_bailleur_id = lfb.id
                          INNER JOIN domaine_fines_fines dff ON f.id = dff.financement_id
                          WHERE lfb.id_instrument_financier = instrument_financiers.id
                          AND dff.domaine_financement_id = domaine_financements.id
                          AND f.status != "brouillon"
                          ' . ($request->filled('annee_id') ? 'AND f.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ' . ($request->filled('status') ? 'AND f.status = "' . $request->status . '"' : '') . '
                          ) as nombre_financements'),
                // Sous-requête pour la somme des montants d'instrument
                DB::raw('(SELECT COALESCE(SUM(DISTINCT CAST(lfb2.montant_total AS DECIMAL(15,2))), 0)
                          FROM ligne_financement_bailleurs lfb2
                          INNER JOIN ligne_fine_bailleurs_fines lbf2 ON lfb2.id = lbf2.ligne_financement_bailleur_id
                          INNER JOIN financements f2 ON lbf2.financement_id = f2.id
                          INNER JOIN domaine_fines_fines dff2 ON f2.id = dff2.financement_id
                          WHERE lfb2.id_instrument_financier = instrument_financiers.id
                          AND dff2.domaine_financement_id = domaine_financements.id
                          AND f2.status != "brouillon"
                          ' . ($request->filled('annee_id') ? 'AND f2.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f2.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f2.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ' . ($request->filled('status') ? 'AND f2.status = "' . $request->status . '"' : '') . '
                          ) as montant_instrument'),
                // Sous-requête pour la somme des montants totaux des projets
                DB::raw('(SELECT COALESCE(SUM(DISTINCT CAST(f3.montant_total AS DECIMAL(15,2))), 0)
                          FROM financements f3
                          INNER JOIN ligne_fine_bailleurs_fines lbf3 ON f3.id = lbf3.financement_id
                          INNER JOIN ligne_financement_bailleurs lfb3 ON lbf3.ligne_financement_bailleur_id = lfb3.id
                          INNER JOIN domaine_fines_fines dff3 ON f3.id = dff3.financement_id
                          WHERE lfb3.id_instrument_financier = instrument_financiers.id
                          AND dff3.domaine_financement_id = domaine_financements.id
                          AND f3.status != "brouillon"
                          ' . ($request->filled('annee_id') ? 'AND f3.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f3.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f3.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ' . ($request->filled('status') ? 'AND f3.status = "' . $request->status . '"' : '') . '
                          ) as montant_total_projet')
            ])
            ->crossJoin('domaine_financements')
            ->whereNotNull('instrument_financiers.libelle')
            ->whereNotNull('domaine_financements.libelle');

        // Appliquer les filtres sur les tables principales si nécessaire
        if ($request->filled('instrument_id')) {
            $query->where('instrument_financiers.id', $request->instrument_id);
        }

        if ($request->filled('domaine_id')) {
            $query->where('domaine_financements.id', $request->domaine_id);
        }

        $statistiques = $query->having('nombre_financements', '>', 0)
            ->orderBy('instrument_financiers.libelle')
            ->orderBy('montant_instrument', 'DESC')
            ->get();

        // Formater les résultats
        $resultat = $statistiques->map(function ($item) {
            return [
                'instrument_id' => $item->instrument_id,
                'instrument' => $item->instrument,
                'domaine_id' => $item->domaine_id,
                'domaine' => $item->domaine,
                'nombre_financements' => (int)$item->nombre_financements,
                'montant_instrument' => (float)$item->montant_instrument,
                'montant_total_projet' => (float)$item->montant_total_projet
            ];
        });

        // Agrégations pour les totaux
        $agregations = [
            'total_projets' => $statistiques->sum('nombre_financements'),
            'total_montant_instrument' => $statistiques->sum('montant_instrument'),
            'total_montant_projets' => $statistiques->sum('montant_total_projet'),
            'nombre_instruments' => $statistiques->unique('instrument_id')->count(),
            'nombre_domaines' => $statistiques->unique('domaine_id')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $resultat,
            'agregations' => $agregations,
            'message' => 'KPI combiné instrument × domaine récupéré avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération du KPI combiné: ' . $e->getMessage()
        ], 500);
    }
}


public function getKpiBeneficiairesCo2ParDomaine(Request $request)
{
    try {
        // SOUS-REQUÊTE pour les projets ACTIFS seulement
        $sousRequeteProjetsActifs = DB::table('financements')
            ->select(
                'financements.id as financement_id',
                'financements.nombre_beneficiaire',
                'financements.volume_co2',
                'financements.montant_total',
                'financements.date_debut',
                'financements.date_fin',
                'financements.status',
                DB::raw('(SELECT annee_id FROM annees_fines WHERE financement_id = financements.id LIMIT 1) as annee_id')
            )
            ->where('financements.status', '!=', 'brouillon');
            // Note: Si vous utilisez SoftDeletes, ajoutez: ->whereNull('financements.deleted_at')

        // CORRECTION : Application correcte des filtres avec des conditions PHP standard
        if ($request->has('annee_id') && $request->annee_id) {
            $sousRequeteProjetsActifs->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('annees_fines')
                    ->whereColumn('annees_fines.financement_id', 'financements.id')
                    ->where('annees_fines.annee_id', $request->annee_id);
            });
        }

        if ($request->has('date_debut') && $request->date_debut) {
            $sousRequeteProjetsActifs->where('financements.date_debut', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $sousRequeteProjetsActifs->where('financements.date_fin', '<=', $request->date_fin);
        }

        // REQUÊTE PRINCIPALE
        $query = DB::table('domaine_financements')
            ->select(
                'domaine_financements.id',
                'domaine_financements.libelle as domaine',

                // Compter UNIQUEMENT les projets qui existent dans financements
                DB::raw('COUNT(DISTINCT CASE
                    WHEN projets_actifs.financement_id IS NOT NULL
                    THEN domaine_fines_fines.financement_id
                    ELSE NULL
                END) as nombre_financements'),

                // Totaux basés sur les projets actifs
                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.nombre_beneficiaire IS NOT NULL
                        AND projets_actifs.nombre_beneficiaire > 0
                        THEN CAST(projets_actifs.nombre_beneficiaire AS DECIMAL(15,2))
                        ELSE 0
                    END
                ), 0) as total_beneficiaires'),

                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.volume_co2 IS NOT NULL
                        AND projets_actifs.volume_co2 > 0
                        THEN CAST(projets_actifs.volume_co2 AS DECIMAL(15,2))
                        ELSE 0
                    END
                ), 0) as total_co2'),

                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.montant_total IS NOT NULL
                        THEN CAST(projets_actifs.montant_total AS DECIMAL(15,2))
                        ELSE 0
                    END
                ), 0) as montant_total'),

                // Moyennes calculées uniquement sur les projets actifs avec valeurs
                DB::raw('CASE
                    WHEN COUNT(DISTINCT CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.nombre_beneficiaire IS NOT NULL
                        AND projets_actifs.nombre_beneficiaire > 0
                        THEN domaine_fines_fines.financement_id
                    END) > 0
                    THEN COALESCE(SUM(
                        CASE
                            WHEN projets_actifs.financement_id IS NOT NULL
                            AND projets_actifs.nombre_beneficiaire IS NOT NULL
                            AND projets_actifs.nombre_beneficiaire > 0
                            THEN CAST(projets_actifs.nombre_beneficiaire AS DECIMAL(15,2))
                            ELSE 0
                        END
                    ), 0) / COUNT(DISTINCT CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.nombre_beneficiaire IS NOT NULL
                        AND projets_actifs.nombre_beneficiaire > 0
                        THEN domaine_fines_fines.financement_id
                    END)
                    ELSE 0
                END as moyenne_beneficiaires'),

                DB::raw('CASE
                    WHEN COUNT(DISTINCT CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.volume_co2 IS NOT NULL
                        AND projets_actifs.volume_co2 > 0
                        THEN domaine_fines_fines.financement_id
                    END) > 0
                    THEN COALESCE(SUM(
                        CASE
                            WHEN projets_actifs.financement_id IS NOT NULL
                            AND projets_actifs.volume_co2 IS NOT NULL
                            AND projets_actifs.volume_co2 > 0
                            THEN CAST(projets_actifs.volume_co2 AS DECIMAL(15,2))
                            ELSE 0
                        END
                    ), 0) / COUNT(DISTINCT CASE
                        WHEN projets_actifs.financement_id IS NOT NULL
                        AND projets_actifs.volume_co2 IS NOT NULL
                        AND projets_actifs.volume_co2 > 0
                        THEN domaine_fines_fines.financement_id
                    END)
                    ELSE 0
                END as moyenne_co2')
            )

            // Jointure avec la table pivot
            ->leftJoin('domaine_fines_fines',
                'domaine_financements.id',
                '=',
                'domaine_fines_fines.domaine_financement_id'
            )

            // Jointure uniquement avec les projets ACTIFS
            ->leftJoinSub($sousRequeteProjetsActifs, 'projets_actifs', function ($join) {
                $join->on('domaine_fines_fines.financement_id', '=', 'projets_actifs.financement_id');
            })

            ->whereNotNull('domaine_financements.libelle')
            ->groupBy('domaine_financements.id', 'domaine_financements.libelle')
            ->orderBy('total_beneficiaires', 'DESC');

        $statistiques = $query->get();

        // DEBUG : Vérifier ce qui est compté
        \Log::info('DEBUG KPI - Statistiques calculées', [
            'total_domaines' => $statistiques->count(),
            'total_projets_comptes' => $statistiques->sum('nombre_financements'),
            'exemple_domaine' => $statistiques->first(),
        ]);

        // Calculer les totaux globaux
        $totalBeneficiaires = $statistiques->sum('total_beneficiaires');
        $totalCo2 = $statistiques->sum('total_co2');
        $totalMontant = $statistiques->sum('montant_total');
        $totalFinancements = $statistiques->sum('nombre_financements');

        // Formater les résultats
        $resultat = $statistiques->map(function($item) use ($totalBeneficiaires, $totalCo2, $totalMontant) {
            return [
                'id' => $item->id,
                'domaine' => $item->domaine,
                'nombre_financements' => (int)$item->nombre_financements,
                'total_beneficiaires' => (float)$item->total_beneficiaires,
                'moyenne_beneficiaires' => (float)$item->moyenne_beneficiaires,
                'total_co2' => (float)$item->total_co2,
                'moyenne_co2' => (float)$item->moyenne_co2,
                'montant_total' => (float)$item->montant_total,
                'pourcentage_beneficiaires' => $totalBeneficiaires > 0 ?
                    round(($item->total_beneficiaires / $totalBeneficiaires) * 100, 2) : 0,
                'pourcentage_co2' => $totalCo2 > 0 ?
                    round(($item->total_co2 / $totalCo2) * 100, 2) : 0,
                'pourcentage_montant' => $totalMontant > 0 ?
                    round(($item->montant_total / $totalMontant) * 100, 2) : 0,
                'beneficiaires_par_million' => $item->montant_total > 0 ?
                    round(($item->total_beneficiaires / $item->montant_total) * 1000000, 2) : 0,
                'co2_par_million' => $item->montant_total > 0 ?
                    round(($item->total_co2 / $item->montant_total) * 1000000, 2) : 0
            ];
        });

        // Agrégations globales
        $agregations = [
            'total_beneficiaires' => $totalBeneficiaires,
            'total_co2' => $totalCo2,
            'total_montant' => $totalMontant,
            'total_financements' => $totalFinancements,
            'moyenne_beneficiaires_par_projet' => $totalFinancements > 0 ?
                $totalBeneficiaires / $totalFinancements : 0,
            'moyenne_co2_par_projet' => $totalFinancements > 0 ?
                $totalCo2 / $totalFinancements : 0,
            'efficacite_beneficiaires' => $totalMontant > 0 ?
                round(($totalBeneficiaires / $totalMontant) * 1000000, 2) : 0,
            'efficacite_co2' => $totalMontant > 0 ?
                round(($totalCo2 / $totalMontant) * 1000000, 2) : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $resultat,
            'agregations' => $agregations,
            'message' => 'KPI bénéficiaires et CO2 par domaine récupéré avec succès'
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur KPI bénéficiaires/CO2', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération du KPI: ' . $e->getMessage()
        ], 500);
    }
}

public function getKpiTopDomainesBeneficiairesCo2(Request $request)
{
    try {
        // Top 10 domaines par bénéficiaires
        $topBeneficiaires = DB::table('domaine_financements')
            ->select(
                'domaine_financements.id',
                'domaine_financements.libelle as domaine',
                DB::raw('COALESCE(SUM(CAST(financements.nombre_beneficiaire AS DECIMAL(15,2))), 0) as total_beneficiaires'),
                DB::raw('COUNT(DISTINCT financements.id) as nombre_projets')
            )
            ->leftJoin('domaine_fines_fines', 'domaine_financements.id', '=', 'domaine_fines_fines.domaine_financement_id')
            ->leftJoin('financements', 'domaine_fines_fines.financement_id', '=', 'financements.id')
            ->where('financements.status', '!=', 'brouillon')
            ->where('financements.nombre_beneficiaire', '>', 0)
            ->groupBy('domaine_financements.id', 'domaine_financements.libelle')
            ->orderBy('total_beneficiaires', 'DESC')
            ->limit(10)
            ->get();

        // Top 10 domaines par réduction CO2
        $topCo2 = DB::table('domaine_financements')
            ->select(
                'domaine_financements.id',
                'domaine_financements.libelle as domaine',
                DB::raw('COALESCE(SUM(CAST(financements.volume_co2 AS DECIMAL(15,2))), 0) as total_co2'),
                DB::raw('COUNT(DISTINCT financements.id) as nombre_projets')
            )
            ->leftJoin('domaine_fines_fines', 'domaine_financements.id', '=', 'domaine_fines_fines.domaine_financement_id')
            ->leftJoin('financements', 'domaine_fines_fines.financement_id', '=', 'financements.id')
            ->where('financements.status', '!=', 'brouillon')
            ->where('financements.volume_co2', '>', 0)
            ->groupBy('domaine_financements.id', 'domaine_financements.libelle')
            ->orderBy('total_co2', 'DESC')
            ->limit(10)
            ->get();

        // Domaines avec le meilleur ratio bénéficiaires/CO2
        $topEfficacite = DB::table('domaine_financements')
            ->select(
                'domaine_financements.id',
                'domaine_financements.libelle as domaine',
                DB::raw('COALESCE(SUM(CAST(financements.nombre_beneficiaire AS DECIMAL(15,2))), 0) as total_beneficiaires'),
                DB::raw('COALESCE(SUM(CAST(financements.volume_co2 AS DECIMAL(15,2))), 0) as total_co2'),
                DB::raw('COUNT(DISTINCT financements.id) as nombre_projets'),
                DB::raw('CASE
                    WHEN SUM(CAST(financements.volume_co2 AS DECIMAL(15,2))) > 0
                    THEN SUM(CAST(financements.nombre_beneficiaire AS DECIMAL(15,2))) / SUM(CAST(financements.volume_co2 AS DECIMAL(15,2)))
                    ELSE 0
                END as ratio_beneficiaires_co2')
            )
            ->leftJoin('domaine_fines_fines', 'domaine_financements.id', '=', 'domaine_fines_fines.domaine_financement_id')
            ->leftJoin('financements', 'domaine_fines_fines.financement_id', '=', 'financements.id')
            ->where('financements.status', '!=', 'brouillon')
            ->where('financements.nombre_beneficiaire', '>', 0)
            ->where('financements.volume_co2', '>', 0)
            ->groupBy('domaine_financements.id', 'domaine_financements.libelle')
            ->orderBy('ratio_beneficiaires_co2', 'DESC')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'top_beneficiaires' => $topBeneficiaires,
                'top_co2' => $topCo2,
                'top_efficacite' => $topEfficacite
            ],
            'message' => 'Top domaines par bénéficiaires et CO2 récupérés avec succès'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}

public function getKpiEvolutionBeneficiairesCo2(Request $request)
{
    try {
        $query = DB::table('annees')
            ->select(
                'annees.id',
                'annees.libelle as annee',
                DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
                DB::raw('COALESCE(SUM(CAST(financements.nombre_beneficiaire AS DECIMAL(15,2))), 0) as total_beneficiaires'),
                DB::raw('COALESCE(SUM(CAST(financements.volume_co2 AS DECIMAL(15,2))), 0) as total_co2'),
                DB::raw('COALESCE(SUM(CAST(financements.montant_total AS DECIMAL(15,2))), 0) as montant_total')
            )
            ->leftJoin('annees_fines', 'annees.id', '=', 'annees_fines.annee_id')
            ->leftJoin('financements', 'annees_fines.financement_id', '=', 'financements.id')
            ->leftJoin('domaine_fines_fines', 'financements.id', '=', 'domaine_fines_fines.financement_id')
            ->leftJoin('domaine_financements', 'domaine_fines_fines.domaine_financement_id', '=', 'domaine_financements.id')
            ->where('financements.status', '!=', 'brouillon')
            ->where(function($query) {
                $query->where('financements.nombre_beneficiaire', '>', 0)
                      ->orWhere('financements.volume_co2', '>', 0);
            });

        if ($request->has('domaine_id') && $request->domaine_id) {
            $query->where('domaine_financements.id', $request->domaine_id);
        }

        $statistiques = $query->groupBy('annees.id', 'annees.libelle')
            ->orderBy('annees.libelle', 'ASC')
            ->get();

        // Calculer les évolutions
        $dataAvecEvolution = [];
        $previous = null;

        foreach ($statistiques as $index => $item) {
            $evolutionBeneficiaires = $previous ?
                round((($item->total_beneficiaires - $previous->total_beneficiaires) / $previous->total_beneficiaires) * 100, 2) : 0;

            $evolutionCo2 = $previous ?
                round((($item->total_co2 - $previous->total_co2) / $previous->total_co2) * 100, 2) : 0;

            $dataAvecEvolution[] = [
                'annee' => $item->annee,
                'nombre_financements' => (int)$item->nombre_financements,
                'total_beneficiaires' => (float)$item->total_beneficiaires,
                'total_co2' => (float)$item->total_co2,
                'montant_total' => (float)$item->montant_total,
                'evolution_beneficiaires' => $evolutionBeneficiaires,
                'evolution_co2' => $evolutionCo2,
                'beneficiaires_par_million' => $item->montant_total > 0 ?
                    round(($item->total_beneficiaires / $item->montant_total) * 1000000, 2) : 0,
                'co2_par_million' => $item->montant_total > 0 ?
                    round(($item->total_co2 / $item->montant_total) * 1000000, 2) : 0
            ];

            $previous = $item;
        }

        return response()->json([
            'success' => true,
            'data' => $dataAvecEvolution,
            'message' => 'Évolution bénéficiaires et CO2 récupérée avec succès'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}

public function getKpiSecteurRegion(Request $request)
{
    try {
        // Construire la requête principale avec sous-requêtes
        $query = DB::table('secteurs')
            ->select([
                'secteurs.id as secteur_id',
                'secteurs.libelle as secteur',
                'regions.id as region_id',
                'regions.nom_region as region',
                // Sous-requête pour compter les financements distincts
                DB::raw('(SELECT COUNT(DISTINCT f.id)
                          FROM financements f
                          INNER JOIN ligne_fine_secteurs_fines lsf ON f.id = lsf.financement_id
                          INNER JOIN ligne_financement_secteurs lfs ON lsf.ligne_financement_secteur_id = lfs.id
                          INNER JOIN ligne_fine_zones_fines lzf ON f.id = lzf.financement_id
                          INNER JOIN ligne_financement_zones lfz ON lzf.ligne_financement_zone_id = lfz.id
                          WHERE lfs.id_secteur = secteurs.id
                          AND lfz.id_region = regions.id
                          AND f.status != "brouillon"
                          AND lfs.montant_total > 0
                          ' . ($request->filled('annee_id') ? 'AND f.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ) as nombre_financements'),
                // Sous-requête pour la somme des montants des secteurs
                DB::raw('(SELECT COALESCE(SUM(DISTINCT CAST(lfs2.montant_total AS DECIMAL(15,2))), 0)
                          FROM ligne_financement_secteurs lfs2
                          INNER JOIN ligne_fine_secteurs_fines lsf2 ON lfs2.id = lsf2.ligne_financement_secteur_id
                          INNER JOIN financements f2 ON lsf2.financement_id = f2.id
                          INNER JOIN ligne_fine_zones_fines lzf2 ON f2.id = lzf2.financement_id
                          INNER JOIN ligne_financement_zones lfz2 ON lzf2.ligne_financement_zone_id = lfz2.id
                          WHERE lfs2.id_secteur = secteurs.id
                          AND lfz2.id_region = regions.id
                          AND f2.status != "brouillon"
                          AND lfs2.montant_total > 0
                          ' . ($request->filled('annee_id') ? 'AND f2.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f2.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f2.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ) as montant_total'),
                // Sous-requête pour la somme des montants des zones
                DB::raw('(SELECT COALESCE(SUM(DISTINCT CAST(lfz3.montant_total AS DECIMAL(15,2))), 0)
                          FROM ligne_financement_zones lfz3
                          INNER JOIN ligne_fine_zones_fines lzf3 ON lfz3.id = lzf3.ligne_financement_zone_id
                          INNER JOIN financements f3 ON lzf3.financement_id = f3.id
                          INNER JOIN ligne_fine_secteurs_fines lsf3 ON f3.id = lsf3.financement_id
                          INNER JOIN ligne_financement_secteurs lfs3 ON lsf3.ligne_financement_secteur_id = lfs3.id
                          WHERE lfs3.id_secteur = secteurs.id
                          AND lfz3.id_region = regions.id
                          AND f3.status != "brouillon"
                          AND lfs3.montant_total > 0
                          ' . ($request->filled('annee_id') ? 'AND f3.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f3.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f3.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ) as montant_total_zone')
            ])
            ->crossJoin('regions')
            ->whereNotNull('secteurs.libelle')
            ->whereNotNull('regions.nom_region');

        // Appliquer les filtres sur les tables principales si nécessaire
        if ($request->filled('secteur_id')) {
            $query->where('secteurs.id', $request->secteur_id);
        }

        if ($request->filled('region_id')) {
            $query->where('regions.id', $request->region_id);
        }

        $statistiques = $query->having('nombre_financements', '>', 0)
            ->orderBy('secteurs.libelle')
            ->orderBy('montant_total', 'DESC')
            ->get();

        // Formatage des résultats en matrice
        $matrice = [];
        $regionsUniques = $statistiques->pluck('region_id', 'region')->unique();
        $secteursUniques = $statistiques->pluck('secteur_id', 'secteur')->unique();

        // Initialiser la matrice
        foreach ($secteursUniques as $secteur => $secteurId) {
            $matrice[$secteur] = [
                'secteur_id' => $secteurId,
                'secteur' => $secteur,
                'regions' => []
            ];

            foreach ($regionsUniques as $region => $regionId) {
                $matrice[$secteur]['regions'][$region] = [
                    'region_id' => $regionId,
                    'region' => $region,
                    'nombre_financements' => 0,
                    'montant_total' => 0,
                    'montant_total_zone' => 0
                ];
            }
        }

        // Remplir la matrice avec les données réelles
        foreach ($statistiques as $stat) {
            if (isset($matrice[$stat->secteur]['regions'][$stat->region])) {
                $matrice[$stat->secteur]['regions'][$stat->region] = [
                    'region_id' => $stat->region_id,
                    'region' => $stat->region,
                    'nombre_financements' => (int)$stat->nombre_financements,
                    'montant_total' => (float)$stat->montant_total,
                    'montant_total_zone' => (float)$stat->montant_total_zone
                ];
            }
        }

        // Convertir en format simple pour le frontend
        $resultat = collect($matrice)->map(function($secteur) {
            $secteur['regions'] = collect($secteur['regions'])->values();
            return $secteur;
        })->values();

        // Agrégations
        $agregations = [
            'total_projets' => $statistiques->sum('nombre_financements'),
            'total_montant' => $statistiques->sum('montant_total'),
            'total_montant_zone' => $statistiques->sum('montant_total_zone'),
            'nombre_secteurs' => $secteursUniques->count(),
            'nombre_regions' => $regionsUniques->count(),
            'regions_liste' => $regionsUniques->keys(),
            'secteurs_liste' => $secteursUniques->keys()
        ];

        return response()->json([
            'success' => true,
            'data' => $resultat,
            'agregations' => $agregations,
            'message' => 'KPI secteur × région récupéré avec succès'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération du KPI secteur × région: ' . $e->getMessage()
        ], 500);
    }
}

public function getKpiCombineBailleurDomaine(Request $request)
{
    try {
        // Construire la requête principale avec sous-requêtes
        $query = DB::table('bailleurs')
            ->select([
                'bailleurs.id as bailleur_id',
                'bailleurs.nom as bailleur',
                'domaine_financements.id as domaine_id',
                'domaine_financements.libelle as domaine',
                // Sous-requête pour compter les financements distincts
                DB::raw('(SELECT COUNT(DISTINCT f.id)
                          FROM financements f
                          INNER JOIN ligne_fine_bailleurs_fines lbf ON f.id = lbf.financement_id
                          INNER JOIN ligne_financement_bailleurs lfb ON lbf.ligne_financement_bailleur_id = lfb.id
                          INNER JOIN domaine_fines_fines dff ON f.id = dff.financement_id
                          WHERE lfb.id_bailleur = bailleurs.id
                          AND dff.domaine_financement_id = domaine_financements.id
                          AND f.status != "brouillon"
                          ' . ($request->filled('annee_id') ? 'AND f.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ' . ($request->filled('status') ? 'AND f.status = "' . $request->status . '"' : '') . '
                          ' . ($request->filled('instrument_id') ? 'AND lfb.id_instrument_financier = ' . (int)$request->instrument_id : '') . '
                          ) as nombre_financements'),
                // Sous-requête pour la somme des montants du bailleur
                DB::raw('(SELECT COALESCE(SUM(DISTINCT CAST(lfb2.montant_total AS DECIMAL(15,2))), 0)
                          FROM ligne_financement_bailleurs lfb2
                          INNER JOIN ligne_fine_bailleurs_fines lbf2 ON lfb2.id = lbf2.ligne_financement_bailleur_id
                          INNER JOIN financements f2 ON lbf2.financement_id = f2.id
                          INNER JOIN domaine_fines_fines dff2 ON f2.id = dff2.financement_id
                          WHERE lfb2.id_bailleur = bailleurs.id
                          AND dff2.domaine_financement_id = domaine_financements.id
                          AND f2.status != "brouillon"
                          ' . ($request->filled('annee_id') ? 'AND f2.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f2.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f2.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ' . ($request->filled('status') ? 'AND f2.status = "' . $request->status . '"' : '') . '
                          ' . ($request->filled('instrument_id') ? 'AND lfb2.id_instrument_financier = ' . (int)$request->instrument_id : '') . '
                          ) as montant_bailleur'),
                // Sous-requête pour la somme des montants totaux des projets
                DB::raw('(SELECT COALESCE(SUM(DISTINCT CAST(f3.montant_total AS DECIMAL(15,2))), 0)
                          FROM financements f3
                          INNER JOIN ligne_fine_bailleurs_fines lbf3 ON f3.id = lbf3.financement_id
                          INNER JOIN ligne_financement_bailleurs lfb3 ON lbf3.ligne_financement_bailleur_id = lfb3.id
                          INNER JOIN domaine_fines_fines dff3 ON f3.id = dff3.financement_id
                          WHERE lfb3.id_bailleur = bailleurs.id
                          AND dff3.domaine_financement_id = domaine_financements.id
                          AND f3.status != "brouillon"
                          ' . ($request->filled('annee_id') ? 'AND f3.id IN (SELECT financement_id FROM annees_fines WHERE annee_id = ' . (int)$request->annee_id . ')' : '') . '
                          ' . ($request->filled('date_debut') ? 'AND f3.date_debut >= "' . $request->date_debut . '"' : '') . '
                          ' . ($request->filled('date_fin') ? 'AND f3.date_fin <= "' . $request->date_fin . '"' : '') . '
                          ' . ($request->filled('status') ? 'AND f3.status = "' . $request->status . '"' : '') . '
                          ' . ($request->filled('instrument_id') ? 'AND lfb3.id_instrument_financier = ' . (int)$request->instrument_id : '') . '
                          ) as montant_total_projet')
            ])
            ->crossJoin('domaine_financements')
            ->whereNotNull('bailleurs.nom')
            ->whereNotNull('domaine_financements.libelle');

        // Appliquer les filtres sur les tables principales si nécessaire
        if ($request->filled('bailleur_id')) {
            $query->where('bailleurs.id', $request->bailleur_id);
        }

        if ($request->filled('domaine_id')) {
            $query->where('domaine_financements.id', $request->domaine_id);
        }

        $statistiques = $query->having('nombre_financements', '>', 0)
            ->orderBy('bailleurs.nom')
            ->orderBy('montant_bailleur', 'DESC')
            ->get();

        // Formater les résultats
        $resultat = $statistiques->map(function ($item) {
            return [
                'bailleur_id' => $item->bailleur_id,
                'bailleur' => $item->bailleur,
                'domaine_id' => $item->domaine_id,
                'domaine' => $item->domaine,
                'nombre_financements' => (int)$item->nombre_financements,
                'montant_bailleur' => (float)$item->montant_bailleur,
                'montant_total_projet' => (float)$item->montant_total_projet
            ];
        });

        // Agrégations pour les totaux
        $agregations = [
            'total_projets' => $statistiques->sum('nombre_financements'),
            'total_montant_bailleur' => $statistiques->sum('montant_bailleur'),
            'total_montant_projets' => $statistiques->sum('montant_total_projet'),
            'nombre_bailleurs' => $statistiques->unique('bailleur_id')->count(),
            'nombre_domaines' => $statistiques->unique('domaine_id')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $resultat,
            'agregations' => $agregations,
            'message' => 'KPI combiné bailleur × domaine récupéré avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération du KPI combiné bailleur × domaine: ' . $e->getMessage()
        ], 500);
    }
}
}
