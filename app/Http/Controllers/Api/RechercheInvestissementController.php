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

        $annee = $input['annee'];
        $monnaie = $input['monnaie'];
        $region = $input['region'];
        $dimension = $input['dimension'];
        $pilier = $input['pilier'];
        $axe = $input['axe'];

        $source = $input['source'];
        $type_source = $input['type_source'];
        $structure= $input['structure'];
        $departement= $input['departement'];

        $validator = Validator::make($input, ['annee' => '','monnaie' => '','region' => '','dimension' => '','pilier' => '','axe' => '','source' => '','type_source' => '','structure' => '','departement' => '']);
        if ($validator->fails())
        {
            return response()
            ->json($validator->errors());
        }
        else{ 
            $investissements = Investissement::with('region')
            ->with('annee')
            ->with('monnaie')
            ->with('structure')
            ->with('source')
            ->with('dimension')
            ->with('mode_financements')
            ->with('ligne_financements')
            ->with('piliers')
            ->with('axes')
            ->with('fichiers');

            if($annee!=null){               
                $investissements->whereHas('annee', function($q) use ($annee){
                    $q->where('id', $annee);
                });
            }
            if($monnaie!=null){               
                $investissements->whereHas('monnaie', function($q) use ($monnaie){
                    $q->where('id', $monnaie);
                });
            }
            if($region!=null){               
                $investissements->whereHas('region', function($q) use ($region){
                    $q->where('id', $region);
                });
            }
            if($dimension!=null){               
                $investissements->whereHas('dimension', function($q) use ($dimension){
                    $q->where('id', $dimension);
                });
            }
            if($pilier!=null){               
                $investissements->whereHas('piliers', function($q) use ($pilier){
                    $q->where('id', $pilier);
                });
            }
            if($axe!=null){               
                $investissements->whereHas('axes', function($q) use ($axe){
                    $q->where('id', $axe);
                });
            }
            /* if($structure!=null){               
                $investissements->whereHas('structure', function($q) use ($structure){
                    $q->where('id', $structure);
                });
            }
            if($source!=null){               
                $investissements->whereHas('source', function($q) use ($source){
                    $q->where('id', $source);
                });
            }
            if($type_source!=null){               
                $investissements->whereHas('type_source', function($q) use ($type_source){
                    $q->where('id', $type_source);
                });
            }
            if($departement!=null){               
                $investissements->whereHas('departement', function($q) use ($departement){
                    $q->where('id', $departement);
                });
            } */

            $investissements->get();

            $total = '$investissements->total()';
            return response()->json(["success" => true, "message" => "Liste des investissements", "data" =>$investissements,"total"=> $total]);
        }
    }
}
