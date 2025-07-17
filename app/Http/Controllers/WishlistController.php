<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle($productId)
    {
        $user = Auth::user();
        $product = Product::findOrFail($productId);

        // Check if product is already in the wishlist
        if ($user->wishlist()->where('product_id', $productId)->exists()) {
            $user->wishlist()->detach($productId);
            return response()->json(['status' => 'removed']);
        } else {
            $user->wishlist()->attach($productId);
            return response()->json(['status' => 'added']);
        }
    }
}
