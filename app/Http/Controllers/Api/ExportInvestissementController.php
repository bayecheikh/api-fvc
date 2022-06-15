<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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

use File;

class ExportInvestissementController extends Controller
{
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCSV(Request $request){

        
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
                $investissements = Investissement::with('region')
                ->with('annee')
                ->with('monnaie')
                ->with('structure')
                ->with('source')
                ->with('dimension')
                ->with('piliers')
                ->with('axes')
                ->with('mode_financements')
                ->with('ligne_financements')
                ->with('fichiers');
            }
            else{
                if($request->user()->hasRole('directeur_eps')){
                    $source_id = User::find($request->user()->id)->structures[0]->source_financements[0]->id;
                    $investissements = Investissement::with('region')
                    ->with('annee')
                    ->with('monnaie')
                    ->with('structure')
                    ->with('source')
                    ->with('dimension')
                    ->with('piliers')
                    ->with('axes')
                    ->with('mode_financements')
                    ->with('ligne_financements')
                    ->with('fichiers')
                    ->whereHas('source', function($q) use ($source_id){
                        $q->where('id', $source_id);
                    });
                }
                else{
                    $structure_id = User::find($request->user()->id)->structures[0]->id;
                    $investissements = Investissement::with('region')
                    ->with('annee')
                    ->with('monnaie')
                    ->with('structure')
                    ->with('source')
                    ->with('dimension')
                    ->with('piliers')
                    ->with('axes')
                    ->with('mode_financements')
                    ->with('ligne_financements')
                    ->with('fichiers')
                    ->whereHas('structure', function($q) use ($structure_id){
                        $q->where('id', $structure_id);
                    });
                }
                
            }

            if($annee!=null){               
                $investissements = $investissements->whereHas('annee', function($q) use ($annee){
                    $q->where('id', $annee);
                });
            }
            if($monnaie!=null){               
                $investissements = $investissements->whereHas('monnaie', function($q) use ($monnaie){
                    $q->where('id', $monnaie);
                });
            }
            if($region!=null){               
                $investissements = $investissements->whereHas('region', function($q) use ($region){
                    $q->where('id', $region);
                });
            }
            if($dimension!=null){               
                $investissements = $investissements->whereHas('dimension', function($q) use ($dimension){
                    $q->where('id', $dimension);
                });
            }
            if($pilier!=null){               
                $investissements = $investissements->whereHas('piliers', function($q) use ($pilier){
                    $q->where('id', $pilier);
                });
            }
            if($axe!=null){               
                $investissements = $investissements->whereHas('axes', function($q) use ($axe){
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

            $investissements = $investissements->orderBy('created_at', 'DESC')->paginate(10);

        // these are the headers for the csv file. Not required but good to have one incase of system didn't recongize it properly
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );


        //I am storing the csv file in public >> files folder.
        if (!File::exists(public_path()."/files")) {
            File::makeDirectory(public_path() . "/files");
        }

        //creating the download file
        $filename =  public_path("files/investissements.csv");
        $handle = fopen($filename, 'w');

        //adding the first row

        $columns = array('Id',
        'Pilier'
        );

       
        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($investissements[0]->ligne_financements as $investissement) {
                $row['id']  = $investissement->id;
                $row['id_pilier']  = $investissement->id_pilier;
                fputcsv($file, array($row['Id'], $row['Pilier']));
            }

            fclose($file);
        };


        //download command
        return response()->stream($callback, 200, $headers);
        }
    }

    public function exportPDF(){

    }
}
