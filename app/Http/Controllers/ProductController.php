<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;  // Ensure the Category model is imported
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Apply category filter if selected
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category); // Use category_id instead of category
        }

        // Fetch the products based on the filter
        $products = $query->get();

        // Fetch all categories
        $categories = Category::all();  // Fetch categories

        // Pass both products and categories to the view
        return view('shop', compact('products', 'categories'));
    }

    public function store(Request $request)
{
    // Validate incoming data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // image validation
    ]);

    // Handle image upload (if present)
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('product_images', 'public');
    }

    // Create product
    $product = Product::create([
        'name' => $validated['name'],
        'description' => $validated['description'],
        'price' => $validated['price'],
        'category_id' => $validated['category_id'],
        'image' => $imagePath, // Store image path
        'user_id' => auth()->id(), // Assuming the user is authenticated
    ]);

    // Redirect or return a response
    return redirect()->route('products.index')->with('success', 'Product created successfully!');
}

}
