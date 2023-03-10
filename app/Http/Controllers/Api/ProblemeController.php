<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ProblemeRisque;
use App\Models\Probleme;
use App\Models\User;

class ProblemeController extends Controller
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
    public function index(Request $request)
    {
        $Problemes = Probleme::paginate(10); 

        
        
        $total = $Problemes->total();

        return response()->json(["success" => true, "message" => "Liste des Problemes risque", "data" =>$Problemes,"total"=> $total]);   
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function problemeMultipleSearch($term)
    {

            $Problemes = Probleme::where('id', 'like', '%'.$term.'%')->orWhere('id_financement', 'like', '%'.$term.'%')->orWhere('nom_beneficiaire', 'like', '%'.$term.'%')->orWhere('prenom_beneficiaire', 'like', '%'.$term.'%')->orWhere('region', 'like', '%'.$term.'%')->orWhere('departement', 'like', '%'.$term.'%')->orWhere('commune', 'like', '%'.$term.'%')->paginate(10);  
             
        
        $total = $Problemes->total();
        return response()->json(["success" => true, "message" => "Liste des Probleme risque", "data" => $Problemes,"total"=> $total]);   
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function problemeByFinancement($idFinancement)
    {

            $Problemes = Probleme::where('id_financement', 'like', '%'.$idFinancement.'%')->paginate(0);  
             
        
        $total = $Problemes->total();
        return response()->json(["success" => true, "message" => "Liste des Probleme risque", "data" => $Problemes,"total"=> $total]);   
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeProbleme($id)
    {
        $Probleme = Probleme::find($id);

        $message = '';

        if($Probleme->status=='actif'){
            $message = 'Probleme desactivé';
            $Probleme->update([
                'status' => 'inactif'
            ]);
        }
        else{
            $message = 'Probleme activé';
            $Probleme->update([
                'status' => 'actif'
            ]);
        }

        return response()->json(["success" => true, "message" => $message, "data" => $Probleme]);   
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
        $validator = Validator::make($input, 
        ['titre_financement' => 'required',
        'id_financement' => 'required', 
        'questionnaire' => 'required'
        ]);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }

        $Probleme = Probleme::create($input);       

        return response()->json(["success" => true, "message" => "Probleme crée avec succès.", "data" => $Probleme]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Probleme = Probleme::find($id);
        if (is_null($Probleme))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Probleme introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Probleme trouvé avec succès.", "data" => $Probleme]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Probleme $Probleme)
    {
        $input = $request->all();
        $validator = Validator::make($input, 
        ['titre_financement' => 'required',
        'id_financement' => 'required', 
        'questionnaire' => 'required'
        ]);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        if(isset($input['id_financement']))
        $Probleme->id_financement= $input['id_financement'];

        if(isset($input['titre_financement']))
        $Probleme->titre_financement= $input['titre_financement'];


        if(isset($input['questionnaire']))
        $Probleme->questionnaire= $input['questionnaire'];

        $Probleme->save();


        return response()
            ->json(["success" => true, "message" => "Probleme modifiée avec succès.", "data" => $Probleme]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Probleme = Probleme::find($id);
        $Probleme->delete();
        return response()
            ->json(["success" => true, "message" => "Probleme supprimée avec succès.", "data" => $Probleme]);
    }
}
