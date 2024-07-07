<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get products",
     *     tags={"Products"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function get () {
        $products = Product::paginate(20);
        return response($products);
    }
    /**
     * @OA\Post(
     *     path="/product/create",
     *     summary="Create product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "name", "price", "image"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="float"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="image", type="file", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'name' => ['required', 'min:3'],
            'price' => ['required', 'integer'],
            'image' => ['required', 'image']
        ]);

        $path = $request->file('image')->store('productImages', 'public');
        $attributes['image'] = asset('public/storage/' . $path);

        return response(Product::create($attributes));
    }

    /**
     * @OA\Get(
     *     path="/products/{product:id}",
     *     summary="Show product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show (Product $product) {
        return response($product);
    }

    /**
     * @OA\Patch(
     *     path="/products/{product:id}",
     *     summary="Update product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "price"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="float"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="image", type="file", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, Product $product)
    {
        $attributes = $request->validate([
            'category_id'=>['required',Rule::exists('categories', 'id')],
            'name'=>['required', 'min:3'],
            'price'=>['required', 'integer'],
            'image'=>['image']
        ]);
        if ($request->hasFile('image')){
            Storage::delete($product->image);

            $path = $request->file('image')->store('productImages', 'public');
            $attributes['image'] = asset('public/storage/' . $path);
        }
        $product->update($attributes);
        return response($product);
    }


    /**
     * @OA\Delete(
     *     path="/products/{product:id}",
     *     summary="Delete prodcut",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Product $product)
    {
        Storage::delete($product->image);
        $product->delete();
        return response(['message'=>'successfully deleted']);
    }
}
