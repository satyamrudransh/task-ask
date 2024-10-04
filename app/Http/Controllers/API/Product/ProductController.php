<?php
namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Storage;

class ProductController extends Controller
    {

    public function show($id)
        {
        $subCategory = SubCategory::with('category')->find($id);
        return response()->json($subCategory, 201);
        }


    public function store(Request $request)
        {
        // // Validate the form data
        // $request->validate([
        //     'product_name' => 'required|string|max:255',
        //     'product_image' => 'required|mimes:jpeg,png,jpg|max:2048',
        //     'subcategories' => 'required|array',
        //     'subcategories.*' => 'integer|exists:sub_categories,id',
        //     'short_description' => 'required|string',
        //     'features' => 'required|string',
        //     'pdf_files.*' => 'mimes:pdf|max:10000',
        // ]);

        // Store the product image
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('public/products/images');
            }

        // Create the product
        $product = Product::create([
            'name' => $request->product_name,
            'image' => $imagePath,
            'short_description' => $request->short_description,
            'features' => $request->features,
            'status' => $request->status ? 1 : 0,
        ]);

        // Attach selected subcategories to the product (many-to-many relationship)
        $product->subcategories()->attach($request->subcategories); // Insert into pivot table `product_subcategory`


        // Handle PDF uploads
        if ($request->hasFile('pdf_files')) {
            foreach ($request->file('pdf_files') as $pdf) {
                $pdfPath = $pdf->store('public/products/pdfs');
                $product->pdfs()->create(['file_path' => $pdfPath]);
                }
            }

        return response()->json(['message' => 'Product created successfully!'], 201);
        }


    // Product list API
    public function index()
        {
        // Fetch all products with their subcategories and PDFs
       return $products = Product::with(['subcategories', 'pdfs'])->get();

        // Map through the products and add full URLs for images and PDFs
        $products = $products->map(function ($product) {
            // Append full URL to the image
            $product->image_url = url(Storage::url($product->image));

            // Append full URLs to the PDF files
            $product->pdfs = $product->pdfs->map(function ($pdf) {
                $pdf->pdf_url = url(Storage::url($pdf->file_path));
                return $pdf;
                });

            return $product;
            });

        // Return the products as a JSON response
        return response()->json($products, 200);
        }
    }

