<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;

class ShopController extends Controller
{
    public function view($sellerId)
    {
        // ✅ Find seller
        $seller = User::findOrFail($sellerId);

        // ✅ Get only this seller's products
        $products = Product::where('user_id', $seller->id)
            ->latest()
            ->get();

        // ✅ Pass both seller and products to the view
        return view('shop.view-shop', compact('seller', 'products'));
    }
}
