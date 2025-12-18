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
    public function investissementByPilier($idPilier){
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

            ->whereHas('piliers', function($q) use ($idPilier){
            $q->where('id', $idPilier);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par pilier", "data" =>$investissements,"total" =>$total]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function investissementBySecteur($idSecteur){
        $investissements = LigneFinancementSecteur::where('id_secteur', $idSecteur)->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par pilier", "data" =>$investissements,"total" =>$total]);
    }
    public function investissementByAxe($idAxe){
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

            ->whereHas('axes', function($q) use ($idAxe){
            $q->where('id', $idAxe);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par axe", "data" =>$investissements,"total" =>$total]);
    }
    public function investissementByAnnee($idAnnee){
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

        ->whereHas('annee', function($q) use ($idAnnee){
        $q->where('id', $idAnnee);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par annee", "data" =>$investissements,"total" =>$total]);
    }
    public function investissementByRegion($idRegion){
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

        ->whereHas('region', function($q) use ($idRegion){
        $q->where('id', $idRegion);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par region", "data" =>$investissements,"total" =>$total]);
    }
    public function investissementByMonnaie($idMonnaie){
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

        ->whereHas('monnaie', function($q) use ($idMonnaie){
        $q->where('id', $idMonnaie);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par monnaie", "data" =>$investissements,"total" =>$total]);

    }
    public function investissementByStructure($idStructure){
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

        ->whereHas('structure', function($q) use ($idStructure){
        $q->where('id', $idStructure);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par structure", "data" =>$investissements,"total" =>$total]);
    }
    public function investissementByDimension($idDimension){
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

        ->whereHas('dimension', function($q) use ($idDimension){
        $q->where('id', $idDimension);
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par dimension", "data" =>$investissements,"total" =>$total]);
    }
    public function investissementBySource($idSource){
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

        ->whereHas('structure', function($q) use ($idSource){
        $q->whereHas('source_financements', function($q) use ($idSource){
            $q->where('id', $idSource);
            });
        })->paginate(0);
        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des investissements par structure", "data" =>$investissements,"total" =>$total]);
    }

    public function allStats(){
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
        ->whereHas('investissement', function($q) use ($status){
            $q->where('status', 'like', '%publie%');
        })->paginate(0);
        $investissements -> load('investissement.mode_financements');

        $total = $investissements->total();
        return response()->json(["success" => true, "message" => "Liste des lignes financements", "data" =>$investissements,"total" =>$total]);
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
            ->leftJoin('ligne_financement_bailleurs',
                'instrument_financiers.id',
                '=',
                'ligne_financement_bailleurs.id_instrument_financier'
            )
            ->leftJoin('ligne_fine_bailleurs_fines',
                'ligne_financement_bailleurs.id',
                '=',
                'ligne_fine_bailleurs_fines.ligne_financement_bailleur_id'
            )
            ->leftJoin('financements',
                'ligne_fine_bailleurs_fines.financement_id',
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
            $query->leftJoin('domaine_fines_fines',
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

        $resultat = $statistiques->map(function($item) use ($totalMontant) {
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
            ->leftJoin('domaine_fines_fines',
                'domaine_financements.id',
                '=',
                'domaine_fines_fines.domaine_financement_id'
            )
            ->leftJoin('financements',
                'domaine_fines_fines.financement_id',
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
            $query->leftJoin('ligne_fine_bailleurs_fines',
                    'financements.id',
                    '=',
                    'ligne_fine_bailleurs_fines.financement_id'
                )
                ->leftJoin('ligne_financement_bailleurs',
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

        $resultat = $statistiques->map(function($item) use ($totalMontant) {
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

public function getKpiCombineInstrumentDomaine(Request $request)
{
    try {
        $query = DB::table('instrument_financiers')
            ->select(
                'instrument_financiers.id as instrument_id',
                'instrument_financiers.libelle as instrument',
                'domaine_financements.id as domaine_id',
                'domaine_financements.libelle as domaine',
                DB::raw('COUNT(DISTINCT financements.id) as nombre_financements'),
                DB::raw('COALESCE(SUM(CAST(ligne_financement_bailleurs.montant_total AS DECIMAL(15,2))), 0) as montant_instrument'),
                DB::raw('COALESCE(SUM(CAST(financements.montant_total AS DECIMAL(15,2))), 0) as montant_total_projet')
            )
            ->leftJoin('ligne_financement_bailleurs',
                'instrument_financiers.id',
                '=',
                'ligne_financement_bailleurs.id_instrument_financier'
            )
            ->leftJoin('ligne_fine_bailleurs_fines',
                'ligne_financement_bailleurs.id',
                '=',
                'ligne_fine_bailleurs_fines.ligne_financement_bailleur_id'
            )
            ->leftJoin('financements',
                'ligne_fine_bailleurs_fines.financement_id',
                '=',
                'financements.id'
            )
            ->leftJoin('domaine_fines_fines',
                'financements.id',
                '=',
                'domaine_fines_fines.financement_id'
            )
            ->leftJoin('domaine_financements',
                'domaine_fines_fines.domaine_financement_id',
                '=',
                'domaine_financements.id'
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
            ->whereNotNull('instrument_financiers.libelle')
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
            $query->where('instrument_financiers.id', $request->instrument_id);
        }

        if ($request->has('domaine_id') && $request->domaine_id) {
            $query->where('domaine_financements.id', $request->domaine_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('financements.status', $request->status);
        }

        $statistiques = $query->groupBy(
                'instrument_financiers.id',
                'instrument_financiers.libelle',
                'domaine_financements.id',
                'domaine_financements.libelle'
            )
            ->orderBy('instrument_financiers.libelle')
            ->orderBy('montant_instrument', 'DESC')
            ->get();

        // Formater les résultats
        $resultat = $statistiques->map(function($item) {
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

}
