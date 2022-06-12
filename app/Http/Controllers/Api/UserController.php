<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Structure;

use Mail;
 
use App\Mail\NotifyMail;

class UserController extends Controller
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
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $users = User::with('roles')->with('structures')->paginate(10);
        }
        else{
            $structure_id = User::find($request->user()->id)->structures[0]->id;
            $users = User::with('roles')->with('structures')->whereHas('structures', function($q) use ($structure_id){
                $q->where('id', $structure_id);
            })->paginate(10);
        }
        
        $total = $users->total();

        return response()->json(["success" => true, "message" => "Liste des utilisateurs", "data" =>$users,"total"=> $total]);   
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userMultipleSearch($term)
    {
        $users = User::where('id', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')->orWhere('name', 'like', '%'.$term.'%')->with('roles')->paginate(5);
        return response()->json(["success" => true, "message" => "Liste des utilisateurs", "data" => $users]);   
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeUser($id)
    {
        $user = User::find($id);

        $message = '';

        if($user->status=='actif'){
            $message = 'Utilisateur desactivé';
            $user->update([
                'status' => 'inactif'
            ]);
            $userTokens = $userInstance->tokens;
        }
        else{
            $message = 'Utilisateur activé';
            $user->update([
                'status' => 'actif'
            ]);
        }

        return response()->json(["success" => true, "message" => "Token Utilisateur activé", "data" => $userTokens]);   
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
        $validator = Validator::make($input, ['firstname' => 'required','lastname' => 'required', 'email' => 'required|unique:users,email']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }

        $pwd = bin2hex(openssl_random_pseudo_bytes(4));

        $user = User::create([
            'name' => $input['firstname'].' '.$input['lastname'],
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'email' => $input['email'],
            'telephone' => $input['telephone'],
            'status' => 'actif',
            'password' => bcrypt('@12345678')
        ]);

        $email = $input['email'];
       

        if(isset($input['structure_id'])){
            $structureObj = Structure::where('id',$input['structure_id'])->first();
            $user->structures()->attach($structureObj);
        }

        $array_roles = $request->roles;

        if(!empty($array_roles)){
            foreach($array_roles as $role){
                $roleObj = Role::where('id',$role)->first();
                $user->roles()->attach($roleObj);
            }
        }

        /* Mail::send('mail',  ['data' => $pwd] , function($message) use($email)
        {   
            $message->to($email)->subject('Nouvelle inscription');
        }); */

        return response()->json(["success" => true, "message" => "Utilisateur créé avec succès.", "data" => $user]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->with('structures')->get()->find($id);
        if (is_null($user))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Utilisateur introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Utilisateur trouvé avec succès.", "data" => $user]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['name' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }

        $user->name = $input['name'];
        $user->firstname = $input['firstname'];
        $user->lastname = $input['lastname'];
        $user->email = $input['email'];
        $user->telephone = $input['telephone'];
        $user->fonction = $input['fonction'];
        $user->save();

        $array_roles = $request->roles;
        $old_roles = $user->roles();

        if(!empty($array_roles)){
            foreach($old_roles as $role){
                $roleObj = Role::where('id',$role)->first();
                $user->roles()->detach($roleObj);
            }
            foreach($array_roles as $role){
                $roleObj = Role::where('id',$role)->first();
                $user->roles()->attach($roleObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "Utilisateur modifié avec succès.", "data" => $user]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()
            ->json(["success" => true, "message" => "Utilisateur supprimé avec succès.", "data" => $user]);
    }
}
