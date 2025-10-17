<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle($productId)
    {
        // ✅ If not logged in, return JSON response instead of breaking
        if (!Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'You must be logged in to use the wishlist.'
            ], 401);
        }

        $user = Auth::user();
        $product = Product::find($productId);

        // ✅ Handle invalid product ID
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }

        // ✅ Toggle wishlist
        if ($user->wishlist()->where('product_id', $productId)->exists()) {
            $user->wishlist()->detach($productId);
            return response()->json(['status' => 'removed']);
        } else {
            $user->wishlist()->attach($productId);
            return response()->json(['status' => 'added']);
        }
    }
}
