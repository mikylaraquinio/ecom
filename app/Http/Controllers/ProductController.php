<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = trim(strtolower($request->search));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
        }

        // Category Filtering (ID & Name)
        if ($request->filled('category')) {
            $categoryInput = $request->category;

            $category = Category::where('id', $categoryInput)
                ->orWhere('name', $categoryInput)
                ->first();

            if ($category) {
                if ($category->parent_id === null) {
                    // main category -> include subcategories
                    $subCategoryIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
                    $subCategoryIds[] = $category->id;
                    $query->whereIn('category_id', $subCategoryIds);
                } else {
                    // subcategory -> filter by its ID
                    $query->where('category_id', $category->id);
                }
            }
        }

        // Price Filtering
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('seller')) {
            $query->where('user_id', (int) $request->seller);
        }

        // Stock Availability - Hide Out of Stock Products
        $query->where('stock', '>', 0);

        // Sorting
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

        $query
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews as ratings_count')
            ->withSum(
                [
                    'orderItems as total_sold' => function ($q) {
                        $q->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->where('orders.status', 'completed');
                    }
                ],
                'quantity'
            );

        // Fetch Products + ordered images
        $products = $query
            ->with([
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                }
            ])
            ->get();

        // Fetch Categories in Hierarchical Structure
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();

        // AJAX support
        if ($request->ajax()) {
            return view('partials.product-list', compact('products'))->render();
        }

        return view('shop', compact('products', 'categories'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('search');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['name']);

        return response()->json($products);
    }

    public function myProducts()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        $products = $user->products()
            ->with([
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                }
            ])
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|in:kg,piece,bundle,sack',
            'weight' => 'required|numeric|min:0.01',
            'min_order_qty' => 'nullable|integer|min:1',
            'category' => 'required|exists:categories,id',
        ]);

        // Cover image
        $imagePath = $request->file('image')->store('products', 'public');

        // Create product
        $imagePath = $request->file('image')->store('products', 'public');

        $product = new Product();
        $product->fill([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'weight' => $request->weight,
            'min_order_qty' => $request->min_order_qty ?? 1,
            'image' => $imagePath,
            'image_path' => asset('storage/'.$imagePath), // ✅ hostinger safe
            'category_id' => $request->category,
            'user_id' => auth()->id(),
        ]);
        $product->save();


        // Save gallery[] files (if any)
        if ($request->hasFile('gallery')) {
            $order = 0;
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => $order++,
                ]);
            }
        }

        return back()->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('products.index')
                ->with('error', 'Product not found.');
        }

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // ✅ Validate product fields
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'min_order_qty' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validated);

        // ✅ Handle cover photo replacement (new uploaded file)
        if ($request->hasFile('image')) {
            if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                $maxSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0;
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $product->image,
                    'sort_order' => $maxSort + 1,
                ]);
            }

            $coverPath = $request->file('image')->store('products', 'public');
            $product->image = $coverPath;
            $product->image_path = Storage::url($coverPath);
        }

        // ✅ Remove cover photo if requested
        if ($request->boolean('remove_cover')) {
            if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = null;
            $product->image_path = null;
        }

        // ✅ Remove selected gallery images
        $removeExisting = json_decode($request->input('remove_existing', '[]'), true);
        if (!empty($removeExisting)) {
            foreach ($removeExisting as $path) {
                $relativePath = str_replace([
                    'http://127.0.0.1:8000/storage/',
                    'http://localhost/storage/',
                    '/storage/'
                ], '', $path);

                $image = ProductImage::where('path', 'like', "%{$relativePath}%")->first();
                if ($image) {
                    if (Storage::disk('public')->exists($image->path)) {
                        Storage::disk('public')->delete($image->path);
                    }
                    $image->delete();
                }
            }
        }

        // ✅ Add new gallery images
        if ($request->hasFile('gallery')) {
            $lastSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $file) {
                $lastSort++;
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => $lastSort,
                ]);
            }
        }

        // ✅ Handle cover from existing image
        if ($request->filled('cover_existing')) {
            $coverPath = $request->input('cover_existing');

            if ($product->image !== $coverPath) {
                if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $product->image = $coverPath;
                $product->image_path = Storage::url($coverPath);
            }
        }

        $product->save();

        // ✅ Respond correctly depending on request type
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
            ]);
        }

        // Fallback for normal requests
        return redirect()->back()->with('success', 'Product updated successfully.');
    }



    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        // Ensure user is authenticated and owns the product
        if (!Auth::check() || $product->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized!']);
        }

        // Collect files to delete after commit
        $filesToDelete = [];
        if ($product->image) {
            $filesToDelete[] = $product->image;
        }
        foreach ($product->images as $img) {
            if (!empty($img->path)) {
                $filesToDelete[] = $img->path;
            }
        }

        DB::transaction(function () use ($product) {
            // If you have FK cascade on product_images (recommended), this is enough
            $product->delete();
        });

        DB::afterCommit(function () use ($filesToDelete) {
            foreach ($filesToDelete as $p) {
                if ($p && Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }
        });

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    public function show(Product $product)
    {
        $product->load([
            'category.parent',
            'user',
            'images' => function ($q) {
                $q->orderBy('sort_order')->orderBy('id');
            },
        ]);

        $seller = $product->user;

        // Helper to append file mtime as ?v= to bust cache per-file
        $v = function (?string $relPath) {
            if (!$relPath) {
                return asset('assets/products.jpg'); // fallback (no versioning for asset)
            }
            $disk = Storage::disk('public');
            $url = $disk->url($relPath);
            if ($disk->exists($relPath)) {
                try {
                    $ts = $disk->lastModified($relPath);
                    return $url . '?v=' . $ts;
                } catch (\Throwable $e) {
                    return $url;
                }
            }
            return $url;
        };

        // Cover (legacy single column) – versioned by file mtime
        $mainImage = $product->image
            ? $v($product->image)
            : asset('assets/products.jpg');

        // Build gallery: cover first (versioned), then additional images (each versioned)
        $gallery = [];
        if ($product->image) {
            $gallery[] = $mainImage;
        }
        if ($product->images && $product->images->count()) {
            foreach ($product->images as $img) {
                if (!empty($img->path)) {
                    $gallery[] = $v($img->path);
                }
            }
        }
        if (empty($gallery)) {
            $gallery[] = asset('assets/products.jpg');
        }

        // Ratings, reviews, etc.
        $ratingsCount = (int) $product->reviews()->count();
        $avgRating = $ratingsCount ? round((float) $product->reviews()->avg('rating'), 1) : null;

        $rawBreakdown = $product->reviews()
            ->select('rating', DB::raw('COUNT(*) as c'))
            ->groupBy('rating')
            ->pluck('c', 'rating');

        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = (int) ($rawBreakdown[$i] ?? 0);
            $breakdown[$i] = [
                'count' => $count,
                'pct' => $ratingsCount ? round($count * 100 / $ratingsCount) : 0,
            ];
        }

        $reviewPhotos = $product->reviews()
            ->whereNotNull('photo_path')
            ->pluck('photo_path')
            ->take(12);

        $reviews = $product->reviews()
            ->with('user:id,name,profile_picture')
            ->latest()
            ->take(10)
            ->get();

        $storeStats = [
            'ratings_count' => $ratingsCount,
            'products_count' => $seller ? $seller->products()->count() : 0,
            'response_rate' => $seller->response_rate ?? null,
            'response_time' => $seller->response_time ?? null,
            'member_since' => $seller?->created_at,
            'followers_count' => $seller->followers_count ?? null,
        ];

        return view('productview', compact(
            'product',
            'seller',
            'mainImage',
            'gallery',
            'storeStats',
            'avgRating',
            'ratingsCount',
            'breakdown',
            'reviews',
            'reviewPhotos'
        ));
    }
}
