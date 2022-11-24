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

class LigneFinancementController extends Controller
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

        $annees = ($input['annees'] != '')?explode (",", $input['annees']):NULL; 
        $monnaies = ($input['monnaies'] != '')?explode (",", $input['monnaies']):NULL; 
        $dimensions = ($input['dimensions'] != '')?explode (",", $input['dimensions']):NULL; 
        $type_structure_sources = ($input['type_structure_sources'] != '')?explode (",", $input['type_structure_sources']):NULL; 
        $structure_sources = ($input['structure_sources'] != '')?explode (",", $input['structure_sources']):NULL; 
        $structure_beneficiaires = ($input['structure_beneficiaires'] != '')?explode (",", $input['structure_beneficiaires']):NULL;    
        $regions = ($input['regions'] != '')?explode (",", $input['regions']):NULL;    
        $piliers = ($input['piliers'] != '')?explode (",", $input['piliers']):NULL; 
        $axes = ($input['axes'] != '')?explode (",", $input['axes']):NULL; 
        $structure_enregistrements = ($input['structure_enregistrements'] != '')?explode (",", $input['structure_enregistrements']):NULL; 

        $validator = Validator::make($input, []);
        if ($validator->fails())
        {
            return response()
            ->json($validator->errors());
        }
        else{ 
            $status = 'publie';

            if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
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
                ->with('dimension');
                ;

                $investissements = $investissements
                ->whereHas('investissement', function($q) use ($status){
                    $q->where('status', 'like', '%publie%');
                }); 


            }
            else{
                if($request->user()->hasRole('directeur_eps')){
                    $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
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
                    ->where('id_type_structure_source', $source_id)->where('status', 'like', '%publie%');

                    $investissements = $investissements
                    ->whereHas('investissement', function($q) use ($status){
                        $q->where('status', 'like', '%publie%');
                    });
                    
                }
                else{
                    $structure_id = User::find($request->user()->id)->structures[0]->id;
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
                    ->where('id_structure', $structure_id)->where('status', 'like', '%publie%');

                    $investissements = $investissements
                    ->whereHas('investissement', function($q) use ($status){
                        $q->where('status', 'like', '%publie%');
                    });
                }
                
            }

            if($annees!=null && !empty($annees)){               
                $investissements = $investissements
                ->whereIn('id_annee', $annees);
            } 
            if($monnaies!=null && !empty($monnaies)){               
                $investissements = $investissements
                ->whereIn('id_monnaie', $monnaies);
            }
            if($dimensions!=null && !empty($dimensions)){               
                $investissements = $investissements
                ->whereIn('id_dimension', $dimensions);
            }
            if($piliers!=null && !empty($piliers)){               
                $investissements = $investissements
                ->whereIn('id_pilier', $piliers);
            }
            if($axes!=null && !empty($axes)){               
                $investissements = $investissements
                ->whereIn('id_axe', $axes);
            }
            if($structure_sources!=null && !empty($structure_sources)){               
                $investissements = $investissements
                ->whereIn('id_structure_source', $structure_sources);
            }
            if($type_structure_sources!=null && !empty($type_structure_sources)){               
                $investissements = $investissements
                ->whereIn('id_type_structure_source', $type_structure_sources);
            }
            if($structure_beneficiaires!=null && $structure_beneficiaires){               
                $investissements = $investissements
                ->whereIn('id_structure_beneficiaire', $structure_beneficiaires);
            }
            if($structure_enregistrements!=null && !empty($structure_enregistrements)){               
                $investissements = $investissements
                ->whereIn('id_structure', $structure_enregistrements);
            }
            if($regions!=null && !empty($regions)){               
                $investissements = $investissements
                ->whereIn('id_region', $regions);
            }
             
            
            
            

            $investissements = $investissements->orderBy('created_at', 'DESC')->paginate(10);
            $investissements -> load('investissement.mode_financements');
            

            $total = $investissements->total();
            return response()->json(["success" => true, "message" => "Liste des investissements", "data" =>$investissements,"total"=> $total]);
        }
    }
}
