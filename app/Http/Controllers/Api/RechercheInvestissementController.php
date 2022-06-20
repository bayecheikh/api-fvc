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

            if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
                $investissements = LigneFinancement::with('investissement')
                ->with('pilier')
                ->with('axe');
            }
            else{
                if($request->user()->hasRole('directeur_eps')){
                    $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
                    $investissements = LigneFinancement::with('investissement')
                    ->with('pilier')
                    ->with('axe')
                    ->whereHas('investissement', function($q) use ($source_id){
                        $q->whereHas('source', function($q) use ($source_id){
                            $q->where('id', $source_id);
                        });
                    });
                    
                }
                else{
                    $structure_id = User::find($request->user()->id)->structures[0]->id;
                    $investissements = LigneFinancement::with('investissement')
                    ->with('pilier')
                    ->with('axe')
                    ->whereHas('investissement', function($q) use ($structure_id){
                        $q->whereHas('structure', function($q) use ($structure_id){
                            $q->where('id', $structure_id);
                        });
                    });
                }
                
            }

            if($annee!=null){               
                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($annee){
                    $q->whereHas('annee', function($q) use ($annee){
                        $q->where('id', $annee);
                    });
                });
            }
            if($monnaie!=null){               
                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($monnaie){
                    $q->whereHas('monnaie', function($q) use ($monnaie){
                        $q->where('id', $monnaie);
                    });
                });
            }
            if($region!=null){               
                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($region){
                    $q->whereHas('region', function($q) use ($region){
                        $q->where('id', $region);
                    });
                });
            }
            if($dimension!=null){               
                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($dimension){
                    $q->whereHas('dimension', function($q) use ($dimension){
                        $q->where('id', $dimension);
                    });
                });
            }
            if($pilier!=null){               
                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($pilier){
                    $q->whereHas('piliers', function($q) use ($pilier){
                        $q->where('id', $pilier);
                    });
                });
            }
            if($axe!=null){               
                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($axe){
                    $q->whereHas('axes', function($q) use ($axe){
                        $q->where('id', $axe);
                    });
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
            $status = 'publie';
            $investissements = $investissements
            ->whereHas('investissement', function($q) use ($status){
                $q->where('status', 'like', '%publie%');
            });

            $investissements = $investissements->orderBy('created_at', 'DESC')->paginate(10);
            //$investissements -> load('investissement.annee');
            $investissements -> load('investissement.structure');
            /* $investissements -> load('investissement.source');
            $investissements -> load('investissement.dimension');
            $investissements -> load('investissement.region'); */
            

            $total = $investissements->total();
            return response()->json(["success" => true, "message" => "Liste des investissements", "data" =>$investissements,"total"=> $total]);
        }
    }
}
