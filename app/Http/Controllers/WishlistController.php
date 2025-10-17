<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Product $product)
{
    $user = auth()->user();

    if ($user->wishlist->contains($product->id)) {
        $user->wishlist()->detach($product->id);
        $message = 'Removed from wishlist.';
    } else {
        $user->wishlist()->attach($product->id);
        $message = 'Added to wishlist.';
    }

    return back()->with('success', $message);
}

}
