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
    public function index()
    {
        $products = Product::all();
        $categories = Category::all(); // ✅ Fetch categories
        return view('shop', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('your-view-name', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
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


