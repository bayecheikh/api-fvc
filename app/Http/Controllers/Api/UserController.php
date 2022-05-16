<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

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
    public function index()
    {
 
        $users = User::all();
        return response()->json(["success" => true, "message" => "Users List", "data" => $users]);

        
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
        $validator = Validator::make($input, ['name' => 'required', 'email' => 'required','password' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $array_roles = $request->roles;

        if(!empty($array_roles)){
            foreach($array_roles as $role){
                $roleObj = Role::where('id',$role)->first();
                $user->roles()->attach($roleObj);
            }
        }

        return response()->json(["success" => true, "message" => "User created successfully.", "data" => $user]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (is_null($user))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "User not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "User retrieved successfully.", "data" => $user]);
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
            ->json(["success" => true, "message" => "User updated successfully.", "data" => $user]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $role->delete();
        return response()
            ->json(["success" => true, "message" => "User deleted successfully.", "data" => $user]);
    }
}
