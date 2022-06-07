<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
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
 
        $roles = Role::with('permissions')->get();
        return response()->json(["success" => true, "message" => "Liste des rôles", "data" => $roles]);

        
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
        $validator = Validator::make($input, ['name' => 'required|unique:roles', 'description' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $role = Role::create($input);

        $array_permissions = $request->permissions;

        if(!empty($array_permissions)){
            foreach($array_permissions as $permission){
                $permissionObj = Permission::where('id',$permission)->first();
                $role->permissions()->attach($permissionObj);
            }
        }

        return response()->json(["success" => true, "message" => "Rôle créé avec succès.", "data" => $role]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with('permissions')->get()->find($id);
        if (is_null($role))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Rôle introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Rôle retrouvé avec succès.", "data" => $role]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['description' => 'required']);
        if ($validator->fails())
        {
            return response() ->json($validator->errors());
        }
        $role->name = $input['name'];
        $role->description = $input['description'];
        $role->save();

        $array_permissions = $request->permissions;
        $old_permissions = $role->permissions();

        if(!empty($array_permissions)){
            foreach($old_permissions as $permission){
                $permissionObj = Permission::where('id',$permission)->first();
                $role->permissions()->detach($permissionObj);
            }
            foreach($array_permissions as $permission){
                $permissionObj = Permission::where('id',$permission)->first();
                $role->permissions()->attach($permissionObj);
            }
        }

        return response()
            ->json(["success" => true, "message" => "Rôle modifié avec succès.", "data" => $role]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()
            ->json(["success" => true, "message" => "Rôle supprimé avec succès.", "data" => $role]);
    }
}
