<?php
namespace App\Http\Controllers\API\SubCategory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
    {

    public function show($id)
        {
        $subCategory = SubCategory::with('category')->find($id);
        return response()->json($subCategory, 201);
        }

    public function getActiveSubcategoriesByCategory($id)
        {
        $subcategories = Subcategory::where('category_id', $id)
            ->where('status', 1)
            ->get();
        return response()->json($subcategories);
        }

    public function store(Request $request)
        {
        // return "a";
        // $request->validate([
        //     'category_id' => 'required|exists:categories,id',
        //     'name' => 'required|string|max:255',
        //     'status' => 'required|string|in:active,inactive',
        // ]);

        $subcategory = SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json($subcategory, 201);
        }



    public function update(Request $request, $id)
        {
        // $request->validate([
        //     'category_id' => 'required|exists:categories,id',
        //     'sub_category_name' => 'required|string|max:255',
        // ]);

        $subCategory = SubCategory::findOrFail($id);
        $subCategory->update([
            'name' => $request->subcategory_name,
            'category_id' => $request->category_id,
            'status' => $request->status ? 1 : 0,
        ]);
        return response()->json($subCategory, 201);

        }

    public function destroy($id)
        {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->delete();
        return response()->json($subCategory, 201);

        }


    public function indexSubCategory()
        {
        // $activeCategories = Category::where('status', 1)->get();
        $subCategories = SubCategory::with('category')->get();
        return response()->json($subCategories, 201);

        }

        public function getSubcategories(Request $request)
        {
            // // Validate the incoming request
            // $request->validate([
            //     'category_id' => 'required|integer|exists:categories,id',
            // ]);
    
            // Fetch subcategories where status is 1 (active) and belong to the given category
            $subcategories = SubCategory::where('category_id', $request->category_id)
                ->where('status', 1) // only active subcategories
                ->get();
    
            // Return the list of active subcategories as a JSON response
            return response()->json($subcategories);
        }
    }
