<?php
namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function get () {
        $products = Product::with('category')->paginate(10);
        return ProductResource::collection($products);
    }
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
    public function show (Product $product) {
        return response($product);
    }
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
    public function destroy(Product $product)
    {
        Storage::delete($product->image);
        $product->delete();
        return response(['message'=>'successfully deleted']);
    }
}
