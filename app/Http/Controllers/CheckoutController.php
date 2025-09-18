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
    // ✅ FIXED prepareCheckout
    public function prepareCheckout(Request $request)
    {
        // If coming from cart (selected items)
        $selectedItems = $request->input('selected_items', []);

        if (!empty($selectedItems)) {
            session(['selected_items' => $selectedItems]);
            return response()->json(['redirect_url' => route('checkout.show')]);
        }

        // If coming from Buy Now (single product)
        if ($request->has('product_id')) {
            $productId = $request->input('product_id');
            $quantity = max(1, (int) $request->input('quantity', 1)); // 👈 force int

            $product = \App\Models\Product::findOrFail($productId);

            if ($product->stock < $quantity) {
                return redirect()->back()->with('error', 'Not enough stock available.');
            }

            session([
                'selected_items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => (int) $quantity, // 👈 force int
                        'buy_now' => true,
                    ]
                ]
            ]);

            return redirect()->route('checkout.show');
        }

        return response()->json(['error' => 'No items selected.'], 400);
    }



    // ✅ FIXED showCheckout
    public function showCheckout()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $selectedItems = session('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->route('shop')->with('error', 'No items selected for checkout.');
        }

        $cartItems = collect();

        if (isset($selectedItems[0]['product_id'])) {
            // ✅ Buy Now flow
            foreach ($selectedItems as $item) {
                $product = Product::with('user')->find($item['product_id']);
                if ($product) {
                    // Create a "fake" cart item so checkout page can still use same blade
                    $fakeCart = new Cart([
                        'id' => 0, // fake ID (not in DB)
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'quantity' => (int) ($item['quantity'] ?? 1), // 👈 force int
                    ]);
                    $fakeCart->setRelation('product', $product);
                    $cartItems->push($fakeCart);
                }
            }
        } else {
            // ✅ Cart Checkout flow
            $cartIds = array_map('intval', $selectedItems);
            $cartItems = Cart::where('user_id', $user->id)
                ->whereIn('id', $cartIds)
                ->with('product.user')
                ->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        // ✅ Subtotal
        $subtotal = $cartItems->sum(
            fn($item) => $item->product ? $item->product->price * (int) $item->quantity : 0
        );

        // ✅ Shipping by seller
        $shippingBySeller = [];
        foreach ($cartItems as $item) {
            if (!$item->product || !$item->product->user) {
                continue;
            }

            $sellerId = $item->product->user->id;
            $buyerTown = $user->town ?? 'default_town';
            $sellerTown = $item->product->user->town ?? 'default_town';

            if (!isset($shippingBySeller[$sellerId])) {
                $shippingBySeller[$sellerId] = [
                    'seller' => $item->product->user,
                    'weight' => 0,
                    'shippingFee' => 0,
                ];
            }

            $shippingBySeller[$sellerId]['weight'] += ($item->product->weight ?? 0) * (int) $item->quantity;
        }

        // ✅ Calculate shipping fees
        $totalShipping = 0;
        foreach ($shippingBySeller as $sid => $info) {
            $fee = \App\Helpers\ShippingHelper::calculate(
                $user->town,
                $info['seller']->town,
                $info['weight']
            );
            $shippingBySeller[$sid]['shippingFee'] = $fee;
            $totalShipping += $fee;
        }

        // ✅ Grand total
        $grandTotal = $subtotal + $totalShipping;

        $addresses = $user->addresses;

        return view('checkout', compact(
            'cartItems',
            'user',
            'addresses',
            'subtotal',
            'totalShipping',
            'grandTotal',
            'shippingBySeller'
        ));
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
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
                'payment_method' => 'required|string',
            ]);

            $user = auth()->user();

            // ✅ Pull from request OR session (for Buy Now)
            $selectedItems = $request->selected_items ?? session('selected_items', []);

            if (empty($selectedItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected.'
                ]);
            }

            $cartItems = collect();

            // ✅ Detect Buy Now flow
            if (isset($selectedItems[0]['product_id'])) {
                foreach ($selectedItems as $item) {
                    $product = Product::with('user')->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Not enough stock for {$product->name}"
                        ], 400);
                    }

                    // Fake cart item so checkout flow works
                    $fakeCart = new Cart([
                        'id' => 0,
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                    ]);
                    $fakeCart->setRelation('product', $product);
                    $cartItems->push($fakeCart);
                }
            } else {
                // ✅ Cart Checkout flow
                $cartItems = Cart::where('user_id', $user->id)
                    ->whereIn('id', $selectedItems)
                    ->with('product.user')
                    ->get();
            }

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items found.'
                ]);
            }

            // ✅ Subtotal
            $subtotal = $cartItems->sum(
                fn($item) => $item->product ? $item->product->price * $item->quantity : 0
            );

            // ✅ Shipping
            $shippingBySeller = [];
            foreach ($cartItems as $item) {
                $sellerId = $item->product->user->id;
                $buyerTown = $user->town ?? 'default_town';
                $sellerTown = $item->product->user->town ?? 'default_town';

                if (!isset($shippingBySeller[$sellerId])) {
                    $shippingBySeller[$sellerId] = ['weight' => 0];
                }

                $shippingBySeller[$sellerId]['weight'] += ($item->product->weight ?? 0) * $item->quantity;
                $shippingBySeller[$sellerId]['fee'] = \App\Helpers\ShippingHelper::calculate(
                    $buyerTown,
                    $sellerTown,
                    $shippingBySeller[$sellerId]['weight']
                );
            }

            $totalShipping = array_sum(array_column($shippingBySeller, 'fee'));
            $grandTotal = $subtotal + $totalShipping;

            // ✅ Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'total_amount' => $grandTotal,
                'shipping_fee' => $totalShipping,
            ]);

            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price
                ]);

                // Only delete if it’s a real cart item
                if ($cartItem->id != 0) {
                    $cartItem->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'redirect_url' => route('checkout.success')
            ]);

        } catch (\Exception $e) {
            \Log::error('Checkout failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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
