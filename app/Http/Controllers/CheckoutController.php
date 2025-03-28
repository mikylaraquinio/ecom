<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Product;
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

        // Validate input fields
        $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'floor_unit_number' => 'nullable|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Find the address belonging to the authenticated user
        $address = $user->addresses()->find($addressId);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found.'], 404);
        }

        // Update the address fields
        $address->update([
            'full_name' => $request->input('full_name'),
            'mobile_number' => $request->input('mobile_number'),
            'floor_unit_number' => $request->input('floor_unit_number'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'barangay' => $request->input('barangay'),
            'notes' => $request->input('notes'),
        ]);

        return response()->json(['success' => true, 'message' => 'Address updated successfully!', 'address' => $address]);
    }

    public function checkout()
    {
        $user = auth()->user();
        $addresses = $user->addresses; // Get all addresses
        $defaultAddress = $addresses->first(); // Get the first/default address

        return view('checkout', compact('addresses', 'defaultAddress'));
    }


    // In CheckoutController.php
    public function process(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
                'selected_items' => 'required|array',
                'payment_method' => 'required|string'
            ]);

            // Initialize total amount
            $totalAmount = 0;

            // Calculate the total amount based on selected items
            foreach ($request->selected_items as $itemId) {
                $cartItem = Cart::find($itemId);
                if ($cartItem) {
                    $totalAmount += $cartItem->quantity * $cartItem->product->price;
                }
            }

            // Create the order with total amount
            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $request->address_id,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'total_amount' => $totalAmount, // ✅ Add this field
            ]);

            // Attach selected items to the order
            foreach ($request->selected_items as $itemId) {
                $cartItem = Cart::find($itemId);
                if ($cartItem) {
                    $order->orderItems()->create([
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product->price
                    ]);
                    $cartItem->delete(); // Remove from cart
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'redirect_url' => route('checkout.success')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function saveSelectedAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id'
        ]);

        session(['selected_address_id' => $request->address_id]);

        return response()->json(['success' => true]);
    }


    public function success()
    {
        $products = Product::latest()->take(6)->get(); // Get latest 6 products
        return view('checkout.success', compact('products'));
    }
}
