<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function prepareCheckout(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        // Store selected items in session for checkout page
        session(['selected_items' => $selectedItems]);

        // Redirect to checkout page
        return response()->json(['redirect_url' => route('checkout.show')]);
    }

    public function showCheckout()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login'); // Redirect to login if the user is not authenticated
        }

        // Retrieve selected items from the session
        $selectedItems = session('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->route('shop')->with('error', 'No items selected for checkout.');
        }

        // Retrieve only the cart items that were selected
        $cartItems = Cart::where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        // Retrieve saved addresses for the user
        $addresses = $user->addresses; // Assuming you have the relationship defined in your User model

        // Pass the filtered cart items, user, and addresses to the view
        return view('checkout', compact('cartItems', 'user', 'addresses'));
    }


    // Save New Address
    public function saveAddress(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'floor_unit_number' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
        ]);

        $address = auth()->user()->addresses()->create($validated);
        return response()->json(['success' => true, 'address' => $address]);
    }

    // Update Existing Address
    public function updateAddress(Request $request, $addressId)
    {
        $user = auth()->user();
        $address = $user->addresses()->findOrFail($addressId);

        $address->update([
            'full_name' => $request->input('full_name'),
            'mobile_number' => $request->input('mobile_number'),
            'floor_unit_number' => $request->input('floor_unit_number'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'barangay' => $request->input('barangay'),
            'notes' => $request->input('notes'),
        ]);

        return response()->json(['success' => true, 'address' => $address]);
    }
    public function checkout()
    {
        $user = auth()->user();
        $address = $user->addresses->first(); // Get the user's default or first address
        return view('checkout', compact('address'));
    }

    // In CheckoutController.php
    public function processCheckout(Request $request)
    {
        // Validate the request
        $request->validate([
            'payment_method' => 'required',
            'selected_items' => 'required|array|min:1',
            'selected_address' => 'required|exists:addresses,id',
        ]);

        $paymentMethod = $request->input('payment_method');
        $selectedItems = $request->input('selected_items');
        $selectedAddress = $request->input('selected_address');

        // Process the payment method here (e.g. integrate PayPal or Stripe)

        // Create the order
        // Save order data
        $order = Order::create([
            'user_id' => auth()->id(),
            'address_id' => $selectedAddress,
            'payment_method' => $paymentMethod,
            'status' => 'Pending',
        ]);

        // Add order items (linking to the products in the cart)
        foreach ($selectedItems as $itemId) {
            $cartItem = CartItem::find($itemId);  
            $order->items()->create([            
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
            ]);
        }


        // Clear the user's cart (Optional)
        Cart::where('user_id', auth()->id())->delete();

        // Redirect to confirmation page
        return redirect()->route('checkout.confirmation', ['order' => $order->id]);
    }


    public function confirmation(Order $order)
    {
        return view('checkout.confirmation', compact('order'));
    }
}
