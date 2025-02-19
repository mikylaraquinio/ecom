<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); 
        return view('shop', compact('products'));
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

