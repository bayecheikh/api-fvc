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

    /**
     * Récupère les statistiques de financement par domaine
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKpiFinancementParDomaine(Request $request)
    {
        try {
            // Option 1: Via Query Builder (plus performant pour les agrégations)
            $statistiques = DomaineFinancement::select(
                'domaine_financements.libelle',
                DB::raw('COUNT(DISTINCT financements.id) as nombre_projet'),
                DB::raw('COALESCE(SUM(financements.montant_total), 0) as volume_financement')
            )
            ->leftJoin('domaine_fines_fines', 'domaine_financements.id', '=', 'domaine_fines_fines.domaine_financement_id')
            ->leftJoin('financements', 'domaine_fines_fines.financement_id', '=', 'financements.id')
            ->where(function($query) {
                // Filtrer les financements actifs si nécessaire
                $query->whereNull('financements.status')
                      ->orWhere('financements.status', '!=', 'rejeté');
            })
            ->groupBy('domaine_financements.id', 'domaine_financements.libelle')
            ->orderBy('domaine_financements.libelle')
            ->get();

            // Option 2: Via Eloquent avec eager loading (si préférez la méthode relationnelle)
            // $statistiques = DomaineFinancement::withCount(['financement' => function($query) {
            //     $query->where(function($q) {
            //         $q->whereNull('status')->orWhere('status', '!=', 'rejeté');
            //     });
            // }])
            // ->withSum(['financement' => function($query) {
            //     $query->where(function($q) {
            //         $q->whereNull('status')->orWhere('status', '!=', 'rejeté');
            //     });
            // }], 'montant_total')
            // ->get()
            // ->map(function($domaine) {
            //     return [
            //         'libelle' => $domaine->libelle,
            //         'nombre_projet' => $domaine->financement_count,
            //         'volume_financement' => $domaine->financement_sum_montant_total ?? 0
            //     ];
            // });

            // Formater la réponse
            $resultat = $statistiques->map(function($item) {
                return [
                    'libelle' => $item->libelle,
                    'nombre_projet' => (int)$item->nombre_projet,
                    'volume_financement' => (float)$item->volume_financement
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultat,
                'message' => 'Statistiques récupérées avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Version avec filtres (année, status, etc.)
     */
    public function getKpiFinancementParDomaineFiltre(Request $request)
    {
        try {
            $query = DomaineFinancement::select(
                'domaine_financements.libelle',
                DB::raw('COUNT(DISTINCT financements.id) as nombre_projet'),
                DB::raw('COALESCE(SUM(financements.montant_total), 0) as volume_financement')
            )
            ->leftJoin('domaine_fines_fines', 'domaine_financements.id', '=', 'domaine_fines_fines.domaine_financement_id')
            ->leftJoin('financements', 'domaine_fines_fines.financement_id', '=', 'financements.id')
            ->leftJoin('annees_fines', 'financements.id', '=', 'annees_fines.financement_id')
            ->leftJoin('annees', 'annees_fines.annee_id', '=', 'annees.id');

            // Filtre par année
            if ($request->has('annee_id')) {
                $query->where('annees.id', $request->annee_id);
            }

            // Filtre par status
            if ($request->has('status')) {
                $query->where('financements.status', $request->status);
            }

            // Filtre par date de début
            if ($request->has('date_debut')) {
                $query->where('financements.date_debut', '>=', $request->date_debut);
            }

            // Filtre par date de fin
            if ($request->has('date_fin')) {
                $query->where('financements.date_fin', '<=', $request->date_fin);
            }

            $statistiques = $query->groupBy('domaine_financements.id', 'domaine_financements.libelle')
                ->orderBy('domaine_financements.libelle')
                ->get();

            $resultat = $statistiques->map(function($item) {
                return [
                    'libelle' => $item->libelle,
                    'nombre_projet' => (int)$item->nombre_projet,
                    'volume_financement' => (float)$item->volume_financement
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultat,
                'message' => 'Statistiques récupérées avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }



}
