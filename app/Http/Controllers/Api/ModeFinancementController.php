<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ModeFinancement;
use App\Models\Investissement;

class ModeFinancementController extends Controller
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
 
        $modefinancements = ModeFinancement::with('investissements')->get();
        return response()->json(["success" => true, "message" => "modefinancement List", "data" => $modefinancements]);

        
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required', 'montant' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $modefinancement = ModeFinancement::create($input);

        return response()->json(["success" => true, "message" => "modefinancement created successfully.", "data" => $modefinancement]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modefinancement = ModeFinancement::with('investissements')->find($id);
        if (is_null($modefinancement))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "modefinancement not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "modefinancement retrieved successfully.", "data" => $modefinancement]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ModeFinancement $modefinancement)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['libelle' => 'required', 'montant' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $modefinancement->libelle = $input['libelle'];
        $modefinancement->montant = $input['montant'];
        $modefinancement->save();
        return response()
            ->json(["success" => true, "message" => "modefinancement updated successfully.", "data" => $modefinancement]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModeFinancement $modefinancement)
    {
        $modefinancement->delete();
        return response()
            ->json(["success" => true, "message" => "mode de financement deleted successfully.", "data" => $modefinancement]);
    }
}
