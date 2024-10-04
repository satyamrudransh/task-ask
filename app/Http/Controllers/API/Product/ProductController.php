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
        // Fetch the product by ID, along with its subcategories, PDFs, and titles
        $product = Product::with(['subcategories.category', 'pdfs', 'titles'])->find($id);

        // Check if the product exists
        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
            }

        // Append full URL to the image
        $product->image_url = url(Storage::url($product->image));

        // Append full URLs to the PDF files with headings
        $product->pdfs = $product->pdfs->map(function ($pdf) {
            $pdf->pdf_url = url(Storage::url($pdf->file_path));
            return $pdf;
            });

        // Return the product with all associated data as a JSON response
        return response()->json($product, 200);
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
            'image' => $imagePath ?? null,
            'short_description' => $request->short_description,
            'features' => $request->features,
            'status' => $request->status ? 1 : 0,
        ]);

        // Attach selected subcategories to the product (many-to-many relationship)
        $product->subcategories()->attach($request->subcategories); // Insert into pivot table `product_subcategory`


        // Handle PDF uploads with associated headings
        if ($request->hasFile('pdf_files')) {
            foreach ($request->file('pdf_files') as $index => $pdf) {
                $pdfPath = $pdf->store('public/products/pdfs');
                $heading = $request->pdf_headings[$index] ?? null;
                $product->pdfs()->create(['file_path' => $pdfPath, 'heading' => $heading]);
                }
            }

        // Store titles, headings, and descriptions (assuming Product has many ProductTitle model)
        foreach ($request->titles as $titleData) {
            $product->titles()->create([
                'title' => $titleData['title'],
                'heading' => $titleData['heading'],
                'description' => $titleData['description'],
            ]);
            }

        return response()->json(['message' => 'Product created successfully!'], 201);
        }

    public function update(Request $request, $id)
        {
        // Find the product by ID
        $product = Product::find($id);

        // Check if the product exists
        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
            }

        // Validate the incoming data
        // $request->validate([
        //     'product_name' => 'required|string|max:255',
        //     'product_image' => 'mimes:jpeg,png,jpg|max:2048',
        //     'subcategories' => 'required|array',
        //     'subcategories.*' => 'integer|exists:sub_categories,id',
        //     'short_description' => 'required|string',
        //     'features' => 'required|string',
        //     'pdf_files.*' => 'mimes:pdf|max:10000',
        // ]);

        // Update product image if a new file is uploaded
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('public/products/images');
            $product->image = $imagePath;
            }

        // Update product fields
        $product->name = $request->product_name;
        $product->short_description = $request->short_description;
        $product->features = $request->features;
        $product->status = $request->status ? 1 : 0;
        $product->save();

        // Update product subcategories
        if ($request->has('subcategories')) {
            $product->subcategories()->sync($request->subcategories);
            }

        // Update or add new PDFs with headings
        if ($request->hasFile('pdf_files')) {
            // Delete old PDFs and create new ones
            $product->pdfs()->delete();

            foreach ($request->file('pdf_files') as $index => $pdf) {
                $pdfPath = $pdf->store('public/products/pdfs');
                $heading = $request->pdf_headings[$index] ?? null;
                $product->pdfs()->create(['file_path' => $pdfPath, 'heading' => $heading]);
                }
            } elseif ($request->has('pdf_headings')) {
            // If only headings are updated, modify existing PDFs
            foreach ($product->pdfs as $index => $existingPdf) {
                $existingPdf->heading = $request->pdf_headings[$index] ?? $existingPdf->heading;
                $existingPdf->save();
                }
            }

        // Update titles, headings, and descriptions
        $product->titles()->delete(); // Optional: clear existing titles before adding new ones
        foreach ($request->titles as $titleData) {
            $product->titles()->create([
                'title' => $titleData['title'],
                'heading' => $titleData['heading'],
                'description' => $titleData['description'],
            ]);
            }

        return response()->json(['message' => 'Product updated successfully!'], 200);
        }

    // Product list API
    public function index()
        {

        //   return  $products = Product::with('subcategories')->get();
        //   return  $products = SubCategory::with('products')->get();

        // Fetch all products with their subcategories and PDFs
        $products = Product::with(['subcategories.category', 'pdfs'])->get();

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

    public function destroy($id)
        {
        // Find the Product by ID
        $product = Product::find($id);

        // Check if the Product exists
        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
            }

        // Delete the Product
        $product->delete();

        return response()->json(['message' => 'Record deleted successfully'], 200);
        }

    }

