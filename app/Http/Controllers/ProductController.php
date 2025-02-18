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
}
