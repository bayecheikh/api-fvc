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

        $annee = $input['annee'];
        $monnaie = $input['monnaie'];
        $region = $input['region'];
        $dimension = $input['dimension'];
        $bailleur = $input['bailleur'];
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

            if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('directeur_eps')) {
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
                ;
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
                })->orderBy('created_at', 'DESC');
                
            }

            if($annee!=null){               
                $financements = $financements
                ->whereHas('annee', function($q) use ($annee){
                        $q->where('id', $annee);
                    });
                
            }
            if($monnaie!=null){               
                $financements = $financements
                ->whereHas('monnaie', function($q) use ($monnaie){
                        $q->where('id', $monnaie);
                    });
                
            }
            if($region!=null){               
                $financements = $financements
                ->whereHas('region', function($q) use ($region){
                        $q->where('id', $region);
                 
                });
            }
            if($dimension!=null){               
                $financements = $financements
                ->whereHas('dimension', function($q) use ($dimension){
                        $q->where('id', $dimension);
                    
                });
            }
            if($bailleur!=null){               
                $financements = $financements
                ->whereHas('bailleurs', function($q) use ($bailleur){
                        $q->where('id', $bailleur);
                    });
               
            }
            if($pilier!=null){               
                $financements = $financements
                ->whereHas('piliers', function($q) use ($pilier){
                        $q->where('id', $pilier);
                    });
              
            }
            if($axe!=null){               
                $financements = $financements
                ->whereHas('axes', function($q) use ($axe){
                        $q->where('id', $axe);
                    });
              
            }
            /* if($structure!=null){               
                $financements->whereHas('structure', function($q) use ($structure){
                    $q->where('id', $structure);
                });
            }
            if($source!=null){               
                $financements->whereHas('source', function($q) use ($source){
                    $q->where('id', $source);
                });
            }
            if($type_source!=null){               
                $financements->whereHas('type_source', function($q) use ($type_source){
                    $q->where('id', $type_source);
                });
            }
            if($departement!=null){               
                $financements->whereHas('departement', function($q) use ($departement){
                    $q->where('id', $departement);
                });
            } */
            $status = 'publie';
            $financements = $financements
            ->where('status', 'like', '%publie%');
           

            $financements = $financements->orderBy('created_at', 'DESC')->paginate(20);
            //$financements -> load('investissement.annee');
            
            /* $financements -> load('investissement.source');
            $financements -> load('investissement.dimension');
            $financements -> load('investissement.region'); */
            

            $total = $financements->total();
            return response()->json(["success" => true, "message" => "Liste des investissements", "data" =>$financements,"total"=> $total]);
        }
    }
}
