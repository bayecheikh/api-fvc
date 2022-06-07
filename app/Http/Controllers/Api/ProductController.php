<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:point_focal|admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->hasPermission('create-task')) {
            $products = Product::all();
            return response()->json(["success" => true, "message" => "liste des produits", "data" => $products]);
        }
        else
        return response()->json(["success" => true, "message" => "Vous n'etes pas autorisé"]);
        
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
        $validator = Validator::make($input, ['name' => 'required', 'detail' => 'required']);
        if ($validator->fails())
        {
            //return $this->sendError('Validation Error.', $validator->errors());
            return response()
            ->json(["success" => true, "message" => "Validation Error."],$validator->errors());
        }
        $product = Product::create($input);
        return response()->json(["success" => true, "message" => "Produit créé avec succès.", "data" => $product]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (is_null($product))
        {
   /*          return $this->sendError('Product not found.'); */
            return response()
            ->json(["success" => true, "message" => "Produit introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Produit retrouvé avec succès.", "data" => $product]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['name' => 'required', 'detail' => 'required']);
        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()]);
        }
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->status = $input['status'];
        $product->save();
        return response()
            ->json(["success" => true, "message" => "Produit modifié avec succès.", "data" => $product]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()
            ->json(["success" => true, "message" => "Produit supprimé avec succès.", "data" => $product]);
    }
}

