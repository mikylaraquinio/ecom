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

        if ($request->filled('search')) {
            $search = trim(strtolower($request->search));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // ✅ FIX Stock Availability Filter
        if ($request->filled('stock')) {
            if ($request->stock === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', 0);
            }
        }

        // **Sorting Logic**
        if ($request->filled('sort_by')) {
            if ($request->sort_by === 'low_to_high') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort_by === 'high_to_low') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort_by === 'newest') {
                $query->orderBy('created_at', 'desc');
            }
        }

        // Debugging Log
        Log::info('Filter parameters:', $request->all());

        $products = $query->get();
        $categories = Category::all();

        if ($request->ajax()) {
            return view('partials.product_list', compact('products'))->render();
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0|max:999',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',

        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->category_id = $request->category_id;
        $product->user_id = Auth::check() ? Auth::id() : null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Ensure user is authenticated
        if (!Auth::check() || $product->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized!']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->update($request->except('image'));

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
}