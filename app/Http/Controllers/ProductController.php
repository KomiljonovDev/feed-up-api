<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Get All products",
     *     @OA\RequestBody(
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *         )
     *     )
     * )
     */
    public function get () {
        $products = Product::paginate(20);
        return response($products);
    }
    /**
     * @OA\Post(
     *     path="/product/create",
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *         @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="slug", type="string"),
     *              @OA\Property(property="price", type="number"),
     *              @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'name' => ['required', 'min:3'],
            'slug' => ['required', 'unique:products'],
            'price' => ['required', 'integer'],
            'description' => ['required', 'min:10'],
            'image' => ['required', 'image']
        ]);

        $path = $request->file('image')->store('productImages', 'public');
        $attributes['image'] = asset('public/storage/' . $path);

        return response(Product::create($attributes));
    }
}
