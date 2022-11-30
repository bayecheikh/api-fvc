<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Region;
use App\Models\Departement;
use App\Models\Structure;
use App\Models\Dimension;
use App\Models\TypeZoneIntervention;
use App\Models\TypeSource;
use App\Models\SourceFinancement;
use App\Models\Fichier;
use Mail;
 
use App\Mail\NotifyMail;

class StructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $structures = Structure::with('users')
        ->paginate(10);
        $total = $structures->total();
        return response()->json(["success" => true, "message" => "Liste des structures", "data" =>$structures,"total"=> $total]);
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function structureMultipleSearch($term)
    {
        $structures = Structure::where('id', 'like', '%'.$term.'%')->orWhere('nom_structure', 'like', '%'.$term.'%')
        ->with('users')
        ->paginate(10);
        $total = $structures->total();
        return response()->json(["success" => true, "message" => "liste des structures", "data" =>$structures,"total"=> $total]);  
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectstructure()
    {
        $structures = Structure::with('users')
        ->get();
        return response()->json(["success" => true, "message" => "liste des structures", "data" =>$structures]);  
    }
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
//a ajouter
        /* $nom_structure = $input['nom_structure'];
        $numero_autorisation = $input['numero_autorisation'];
        $accord_siege = $input['accord_siege'];
        $numero_agrement = $input['numero_agrement'];
        $adresse_structure = $input['adresse_structure'];
        $debut_intervention = $input['debut_intervention'];
        $fin_intervention = $input['fin_intervention'];
        $telephone_structure = $input['telephone_structure'];
        $email_structure = $input['email_structure'];

        $source_financements = explode (",", $input['source_financements']);
        $type_sources = explode (",", $input['type_sources']);
        $departements = explode (",", $input['departements']);
        $regions = explode (",", $input['regions']);
        $dimensions = explode (",", $input['dimensions']);
        $type_zone_interventions = explode (",", $input['type_zone_interventions']);

        $firstname_responsable = $input['firstname_responsable'];
        $lastname_responsable = $input['lastname_responsable'];
        $email_responsable = $input['email_responsable'];
        $telephone_responsable = $input['telephone_responsable'];
        $fonction_responsable = $input['fonction_responsable']; */

        $validator = Validator::make($input, ['nom_structure' => 'required']);
        if ($validator->fails())
        {
            return response()
            ->json($validator->errors());
        }
        else{
            $structure = Structure::create(
                ['nom_structure' => $input['nom_structure'],
                'adresse_structure' => $input['adresse_structure'],
                'telephone_structure' => $input['telephone_structure'],
                'email_structure' => $input['email_structure'],
                'status' => 'actif']
            );

            $pwd = bin2hex(openssl_random_pseudo_bytes(4));
            $user = User::create([
                'name' => $input['firstname_responsable'].' '.$input['lastname_responsable'],
                'firstname' => $input['firstname_responsable'],
                'lastname' => $input['lastname_responsable'],
                'email' => $input['email_responsable'],
                'telephone' => $input['telephone_responsable'],
                'fonction' => $input['fonction_responsable'],
                'password' => bcrypt($pwd)
            ]);
            $roleObj = Role::where('name','admin_structure')->first();
            $user->roles()->attach($roleObj);
    
            $structure->users()->attach($user);

            $email = $input['email_responsable'];
            $messages = 'Votre mot de passe par défaut sur la plateforme de suivie des investissement du MSAS est : ';
            $mailData = ['data' => $pwd, 'messages' => $messages];
            Mail::to($email)->send(new NotifyMail($mailData));
        
            return response()->json(["success" => true, "message" => "Structure créée avec succès.", "data" => $structure]);
            //return response()->json(["success" => true, "message" => "Structure created successfully.", "data" => $input]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $structure = Structure::with('users')
        ->get()
        ->find($id);
        $structure->load('users.roles');
        if (is_null($structure))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Structure introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Structure retrouvée avec succès.", "data" => $structure]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Structure $structure)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['nom_structure' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        else{
        $structure->nom_structure = $input['nom_structure'];
        $structure->adresse_structure = $input['adresse_structure'];
        $structure->telephone_structure = $input['telephone_structure'];
        $structure->email_structure = $input['email_structure'];
        $structure->status = 'actif';
        $structure->save();

        return response()
            ->json(["success" => true, "message" => "structure modifiée avec succès.", "data" => $structure]);
    }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Structure $structure)
    {
        $structure->delete();
        return response()
            ->json(["success" => true, "message" => "Structure supprimée avec succès.", "data" => $structure]);
    }
}
