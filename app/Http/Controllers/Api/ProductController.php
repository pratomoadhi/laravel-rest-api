<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    
    /**
    * @OA\Get(
    *     path="/api/products",
    *     operationId="List",
    *     tags={"Product"},
    *     summary="Product List",
    *     description="Product List here",
    *     security={{"api_key":{}}}, 
    *     @OA\Parameter(
    *         name="size",
    *         in="query",
    *         description="Paginate size",
    *         required=false,
    *     ),
    *     @OA\Parameter(
    *         name="page",
    *         in="query",
    *         description="Paginate page",
    *         required=false,
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product retrieved successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(response=400, description="Bad request"),
    *     @OA\Response(response=404, description="Products Not Found"),
    * )
    */
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $products = Product::all();

        if ($request->input('size')) { 
            $size = $request->input('size');
        } else {
            // Set default pagination size
            $size = 10;
        }
        $products = Product::paginate($size);
    
        // return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
        return $this->sendResponse($products, 'Products retrieved successfully.');
    }

    /**
    * @OA\Post(
    *     path="/api/products",
    *     operationId="Create",
    *     tags={"Product"},
    *     summary="Product Create",
    *     description="Product Create here",
    *     security={{"api_key":{}}},
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"name", "detail"},
    *               @OA\Property(property="name", type="text"),
    *               @OA\Property(property="detail", type="text")
    *            ),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Product created successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(response=400, description="Bad request"),
    *     @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::allows('isAdmin')) {
            $input = $request->all();
       
            $validator = Validator::make($input, [
                'name' => 'required',
                'detail' => 'required'
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
       
            $product = Product::create($input);
       
            return $this->sendResponse(new ProductResource($product), 'Product created successfully.', 201);
    
        } else {
            $error = "You are not allowed to POST";
            return $this->sendError($error);
        }
    } 
   
    /**
    * @OA\Get(
    *     path="/api/products/{id}",
    *     operationId="Retrieve",
    *     tags={"Product"},
    *     summary="Product Retrieve",
    *     description="Product Retrieve here",
    *     security={{"api_key":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="Product id",
    *         required=true,
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product retrieved successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(response=400, description="Bad request"),
    *     @OA\Response(response=404, description="Product Not Found"),
    * )
    */
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }
    
    /**
    * @OA\Put(
    *     path="/api/products/{id}",
    *     operationId="Update",
    *     tags={"Product"},
    *     summary="Product Update",
    *     description="Product Update here",
    *     security={{"api_key":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="Product id",
    *         required=true,
    *     ),
    *     @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="Product name",
    *         required=true,
    *     ),
    *     @OA\Parameter(
    *         name="detail",
    *         in="query",
    *         description="Product detail",
    *         required=true,
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product updated successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(response=400, description="Bad request"),
    *     @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
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
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();
   
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    /**
    * @OA\Delete(
    *     path="/api/products/{id}",
    *     operationId="Delete",
    *     tags={"Product"},
    *     summary="Product Delete",
    *     description="Product Delete here",
    *     security={{"api_key":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="Product id",
    *         required=true,
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product deleted successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(response=400, description="Bad request"),
    *     @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
   
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}