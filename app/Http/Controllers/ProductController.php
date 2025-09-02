<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // ✅ Search Filter
        if ($request->filled('search')) {
            $search = trim(strtolower($request->search));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
        }

        // ✅ Category Filtering (ID & Name)
        if ($request->filled('category')) {
            $categoryInput = $request->category;

            // Check if the category input is a valid ID or Name
            $category = Category::where('id', $categoryInput)
                ->orWhere('name', $categoryInput)
                ->first();

            if ($category) {
                if ($category->parent_id === null) {
                    // If it's a main category, include subcategories
                    $subCategoryIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
                    $subCategoryIds[] = $category->id;
                    $query->whereIn('category_id', $subCategoryIds);
                } else {
                    // If it's a subcategory, filter by its ID
                    $query->where('category_id', $category->id);
                }
            }
        }

        // ✅ Price Filtering
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('seller')) {
            $query->where('user_id', (int) $request->seller);
        }

        // ✅ Stock Availability - Hide Out of Stock Products
        $query->where('stock', '>', 0); // 🔥 This line hides products with stock = 0

        // ✅ Sorting Logic
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'low_to_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'high_to_low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        // ✅ Fetch Products
        $products = $query->get();

        // ✅ Fetch Categories in Hierarchical Structure
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();

        // ✅ AJAX Support for Dynamic Filtering
        if ($request->ajax()) {
            return view('partials.product-list', compact('products'))->render();
        }

        return view('shop', compact('products', 'categories'));
    }


    public function autocomplete(Request $request)
    {
        $query = $request->input('search');

        if (!$query || strlen($query) < 2) {
            return response()->json([]); // Return empty if no valid query
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['name']); // Fetch only 'name' for efficiency

        return response()->json($products);
    }


    public function myProducts()
    {
        $user = Auth::user(); // Get the authenticated user

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        $products = $user->products
            ->withCount([
                'orderItems as total_sold' => function ($query) {
                    $query->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', 'completed')
                        ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');
                }
            ])
            ->get();

        return view('seller.my-products', compact('products'));
    }


    public function create()
    {
        $mainCategories = Category::with('subcategories')->get();
        return view('your-view-file', compact('mainCategories'));

    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|in:kg,piece,bundle,sack', // or adjust options
            'min_order_qty' => 'nullable|integer|min:1',
            'category' => 'required|exists:categories,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        } else {
            return back()->with('error', 'Image upload failed.');
        }

        // Create new product
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->unit = $request->unit;
        $product->min_order_qty = $request->min_order_qty ?? 1;
        $product->image = $imagePath; // Store in the 'image' column
        $product->image_path = asset('storage/' . $imagePath); // Store full path in 'image_path'
        $product->category_id = $request->category;
        $product->user_id = auth()->id(); // Make sure user is logged in
        $product->save();

        return redirect()->back()->with('success', 'Product added successfully.');
    }


    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'category' => 'required|exists:categories,id', // Ensure category exists
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    // Assign values
    $product->name = $request->name;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->stock = $request->stock;
    $product->category_id = $request->category; // 🛠️ Fix this!

    // Handle Image Upload (if new image is uploaded)
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($product->image && Storage::exists('public/' . $product->image)) {
            Storage::delete('public/' . $product->image);
        }

        // Store new image
        $path = $request->file('image')->store('products', 'public');
        $product->image = $path;
    }

    // Save updates
    $product->save();

    return redirect()->back()->with('success', 'Product updated successfully!');
}


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Ensure user is authenticated
        if (!Auth::check() || $product->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized!']);
        }

        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    public function show(Product $product)
{
    $product->load(['category.parent', 'user', 'reviews']);

    $seller    = $product->user;
    $mainImage = $product->image ? asset('storage/'.$product->image) : asset('assets/products.jpg');
    $gallery   = [$mainImage];

    $ratingsCount = $product->reviews()->count();
    $avgRating    = round((float) $product->reviews()->avg('rating'), 1);

    $storeStats = [
        'ratings_count'   => $ratingsCount,
        'products_count'  => $seller ? $seller->products()->count() : 0,
        'response_rate'   => $seller->response_rate ?? null,
        'response_time'   => $seller->response_time ?? null,
        'member_since'    => $seller?->created_at,
        'followers_count' => $seller->followers_count ?? null,
    ];

    return view('productview', compact('product','seller','mainImage','gallery','storeStats','avgRating'));
}


}