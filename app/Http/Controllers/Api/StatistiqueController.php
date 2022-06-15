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
use App\Models\ModeFinancement;
use App\Models\LigneModeInvestissement;
use App\Models\Dimension;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Pilier;
use App\Models\Axe;

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
    
}
