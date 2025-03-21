<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Example: Retrieve cart items from session or database
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Example: Process payment (You need to integrate a payment gateway here)
        // Example: Clear the cart after successful checkout
        session()->forget('cart');

        return response()->json(['message' => 'Checkout successful!']);
    }
}
