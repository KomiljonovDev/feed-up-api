<?php
namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function get()
    {
        $categories = Category::paginate(10);
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name'=>['required', 'min:3'],
        ]);
        return response(Category::create($attributes));
    }

    public function show(Category $category)
    {
        return response($category);
    }

    public function update(Request $request, Category $category)
    {
        $attributes = $request->validate([
            'name'=>['required', 'min:3']
        ]);
        $category->update($attributes);
        return response($category);
    }
    public function destroy(Category $category)
    {
        $category->delete();
        return response(['message'=>'successfully deleted']);
    }
}
