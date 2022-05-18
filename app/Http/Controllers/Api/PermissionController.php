<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Permission;

class PermissionController extends Controller
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
 
        $permissions = Permission::all();
        return response()->json(["success" => true, "message" => "Permission List", "data" => $permissions]);

        
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
        $validator = Validator::make($input, ['name' => 'required|unique:permissions', 'description' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json($validator->errors());
        }
        $permission = Permission::create($input);
        return response()->json(["success" => true, "message" => "Permission created successfully.", "data" => $permission]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::find($id);
        if (is_null($permission))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Permission not found."]);
        }
        return response()
            ->json(["success" => true, "message" => "Permission retrieved successfully.", "data" => $permission]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['description' => 'required']);
        if ($validator->fails())
        {
            return response() ->json($validator->errors());
        }
        //$permission->name = $input['name'];
        $permission->description = $input['description'];
        $permission->save();
        return response()
            ->json(["success" => true, "message" => "Permission updated successfully.", "data" => $permission]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()
            ->json(["success" => true, "message" => "Permission deleted successfully.", "data" => $permission]);
    }
}
