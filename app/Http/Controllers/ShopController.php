<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;

class ShopController extends Controller
{
    public function view($sellerId)
    {
        $seller = User::with('seller')->findOrFail($sellerId); // ✅ include seller relationship

        $products = Product::where('user_id', $seller->id)
            ->latest()
            ->get();

        return view('shop.view-shop', compact('seller', 'products'));
    }

}
