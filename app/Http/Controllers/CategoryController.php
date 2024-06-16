<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function get()
    {
        $categories = Category::paginate(10);
        return response($categories);
    }

    /**
     * @OA\Post(
     *     path="/category/create",
     *     summary="Create category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "slug", "image"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="image", type="string", format="binary")
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
            'name'=>['required', 'min:3'],
            'description'=>['required', 'min:10'],
            'slug'=>['required', 'unique:categories'],
            'image'=>['required', 'image']
        ]);
        $path = $request->file('image')->store('categoryImages', 'public');
        $attributes['image'] = asset('storage/' . $path);
        return response(Category::create($attributes));
    }

    /**
     * @OA\Get(
     *     path="/categories/{category:id}",
     *     summary="Show category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Category $category)
    {
        return response($category);
    }

    /**
     * @OA\Patch(
     *     path="/categories/{category:id}",
     *     summary="Update category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "slug"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, Category $category)
    {
        $attributes = $request->validate([
            'name'=>['required', 'min:3'],
            'description'=>['required', 'min:10'],
            'slug'=>['required', Rule::unique('categories', 'slug')->ignore($category->id)],
            'image'=>['image']
        ]);
        if ($request->hasFile('image')){
            Storage::delete($category->image);

            $path = $request->file('image')->store('categoryImages', 'public');
            $attributes['image'] = asset('storage/' . $path);
        }
        $category->update($attributes);
        return response($category);
    }

    /**
     * @OA\Delete(
     *     path="/categories/{category:id}",
     *     summary="Delete category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Category $category)
    {
        Storage::delete($category->image);
        $category->delete();
        return response(['message'=>'successfully deleted']);
    }
}
