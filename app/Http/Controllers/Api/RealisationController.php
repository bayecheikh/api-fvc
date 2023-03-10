<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RealisationRisque;
use App\Models\Realisation;
use App\Models\User;

class RealisationController extends Controller
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
        $Realisations = Realisation::paginate(10); 

        
        
        $total = $Realisations->total();

        return response()->json(["success" => true, "message" => "Liste des Realisations risque", "data" =>$Realisations,"total"=> $total]);   
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function RealisationMultipleSearch($term)
    {

            $Realisations = Realisation::where('id', 'like', '%'.$term.'%')->orWhere('id_financement', 'like', '%'.$term.'%')->orWhere('nom_beneficiaire', 'like', '%'.$term.'%')->orWhere('prenom_beneficiaire', 'like', '%'.$term.'%')->orWhere('region', 'like', '%'.$term.'%')->orWhere('departement', 'like', '%'.$term.'%')->orWhere('commune', 'like', '%'.$term.'%')->paginate(10);  
             
        
        $total = $Realisations->total();
        return response()->json(["success" => true, "message" => "Liste des Realisation risque", "data" => $Realisations,"total"=> $total]);   
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeRealisation($id)
    {
        $Realisation = Realisation::find($id);

        $message = '';

        if($Realisation->status=='actif'){
            $message = 'Realisation desactivé';
            $Realisation->update([
                'status' => 'inactif'
            ]);
        }
        else{
            $message = 'Realisation activé';
            $Realisation->update([
                'status' => 'actif'
            ]);
        }

        return response()->json(["success" => true, "message" => $message, "data" => $Realisation]);   
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

        $Realisation = Realisation::create($input);       

        return response()->json(["success" => true, "message" => "Realisation crée avec succès.", "data" => $Realisation]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Realisation = Realisation::find($id);
        if (is_null($Realisation))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Realisation introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Realisation trouvé avec succès.", "data" => $Realisation]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Realisation $Realisation)
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
        $Realisation->id_financement= $input['id_financement'];

        if(isset($input['titre_financement']))
        $Realisation->titre_financement= $input['titre_financement'];


        if(isset($input['questionnaire']))
        $Realisation->questionnaire= $input['questionnaire'];

        $Realisation->save();


        return response()
            ->json(["success" => true, "message" => "Realisation modifiée avec succès.", "data" => $Realisation]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Realisation = Realisation::find($id);
        $Realisation->delete();
        return response()
            ->json(["success" => true, "message" => "Realisation supprimée avec succès.", "data" => $Realisation]);
    }
}
