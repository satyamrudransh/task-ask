<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Add Category
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'status' => 'boolean',
        // ]);

        $category = Category::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json($category, 201);
    }

    // Get all Categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // Update Category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only('name', 'status'));

        return response()->json($category);
    }

    // Delete Category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }
}
