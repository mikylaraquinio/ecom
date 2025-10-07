<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderForSeller;
use Illuminate\Support\Facades\Notification;
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
                // ⭐ CHANGE #1: eager-load seller profile too
                $product = \App\Models\Product::with('user.seller')->findOrFail($item['product_id']);
                if ($product) {
                    // Create a "fake" cart item so checkout page can still use same blade
                    $fakeCart = new \App\Models\Cart([
                        'id'         => 0, // fake ID (not in DB)
                        'user_id'    => $user->id,
                        'product_id' => $product->id,
                        'quantity'   => (int) ($item['quantity'] ?? 1),
                    ]);
                    $fakeCart->setRelation('product', $product);
                    $cartItems->push($fakeCart);
                }
            }
        } else {
            // ✅ Cart Checkout flow
            $cartIds = array_map('intval', $selectedItems);

            // ⭐ CHANGE #2: eager-load seller profile too
            $cartItems = \App\Models\Cart::where('user_id', $user->id)
                ->whereIn('id', $selectedItems)
                ->with('product.user.seller')   // add .seller here
                ->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        // ✅ Subtotal
        $subtotal = $cartItems->sum(
            fn($item) => $item->product ? $item->product->price * (int) $item->quantity : 0
        );

        // Get addresses early to know the buyer's city
        $addresses = $user->addresses;
        $selectedAddressId = session('selected_address_id');
        $selectedAddr = $selectedAddressId
            ? $addresses->firstWhere('id', (int) $selectedAddressId)
            : $addresses->first();

        $buyerCity = optional($selectedAddr)->city
            ?? ($user->city ?? $user->town ?? '');


        // ✅ Shipping by seller (city → city)
        $shippingBySeller = [];
        foreach ($cartItems as $item) {
            if (!$item->product || !$item->product->user) continue;

            $sellerUser = $item->product->user;        // has ->seller eager-loaded
            $seller     = $sellerUser->seller;

            // Prefer seller pickup city; fall back to seller user city/town
            $sellerCity = optional($seller)->pickup_city
                    ?? ($sellerUser->city ?? $sellerUser->town ?? '');

            $sid = $sellerUser->id;

            if (!isset($shippingBySeller[$sid])) {
                $shippingBySeller[$sid] = [
                    'seller'      => $sellerUser,
                    'buyer_city'  => $buyerCity,
                    'seller_city' => $sellerCity,
                    'weight'      => 0,
                    'shippingFee' => 0,
                ];
            }

            $shippingBySeller[$sid]['weight'] += ($item->product->weight ?? 0) * (int) $item->quantity;
        }

        // ✅ Calculate shipping fees using distance rate (₱16 + ₱12/km)
        $totalShipping = 0;
        foreach ($shippingBySeller as $sid => $info) {
            $fee = \App\Helpers\ShippingHelper::calculate(
                $info['buyer_city'],
                $info['seller_city'],
                $info['weight'] // not used by the new table but kept for signature
            );
            $shippingBySeller[$sid]['shippingFee'] = $fee;
            $totalShipping += $fee;
        }

        // ✅ Grand total
        $grandTotal = $subtotal + $totalShipping;

        $addresses = $user->addresses;

        // ⭐ NEW BLOCK (#3): build per-seller pickup addresses (AFTER $cartItems is ready)
        $pickupBySeller = [];
        foreach ($cartItems as $item) {
            $sellerUser = optional($item->product)->user;
            if (!$sellerUser) continue;

            $sid = $sellerUser->id;
            if (isset($pickupBySeller[$sid])) continue; // only once per seller

            $sp = optional($sellerUser->seller);

            // One-line address (prefer the pre-composed column if you saved it; otherwise assemble)
            $addressLine = $sp?->pickup_address
                ?: collect([
                    $sp?->pickup_detail,
                    $sp?->pickup_barangay,
                    $sp?->pickup_city,
                    $sp?->pickup_province,
                    $sp?->pickup_region_group,
                    $sp?->pickup_postal,
                ])->filter()->implode(', ');

            $pickupBySeller[$sid] = [
                'name'         => $sellerUser->name ?? $sellerUser->username ?? 'Seller',
                'phone'        => $sp?->pickup_phone,
                'address_line' => $addressLine,
            ];
        }

        return view('checkout', compact(
            'cartItems',
            'user',
            'addresses',
            'subtotal',
            'totalShipping',
            'grandTotal',
            'shippingBySeller',
            'pickupBySeller' // ⭐ pass to Blade
        ));
    }






    // Save New Address
    public function saveAddress(Request $request)
    {
    $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'mobile_number'     => 'required|string|max:20',   // align with updateAddress
            'floor_unit_number' => 'nullable|string|max:255',  // was required -> nullable
            'province'          => 'required|string|max:255',
            'city'              => 'required|string|max:255',
            'barangay'          => 'required|string|max:255',
            'notes'             => 'nullable|string|max:500',
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
        
            // ✅ validate including fulfillment_method
            $request->validate([
                'payment_method'      => 'required|in:gcash,cod',
                'fulfillment_method'  => 'required|in:delivery,pickup',            // <—
                'address_id'          => 'required_if:fulfillment_method,delivery' // <— only required for delivery
                                            . '|nullable|exists:addresses,id',
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
                    $product = \App\Models\Product::with('user')->findOrFail($item['product_id']);

                    if ($product->stock < (int)$item['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Not enough stock for {$product->name}"
                        ], 400);
                    }

                    // Fake cart item so checkout flow works
                    $fakeCart = new \App\Models\Cart([
                        'id'         => 0,
                        'user_id'    => $user->id,
                        'product_id' => $product->id,
                        'quantity'   => (int)$item['quantity'],
                    ]);
                    $fakeCart->setRelation('product', $product);
                    $cartItems->push($fakeCart);
                }
            } else {
                // ✅ Cart Checkout flow
                $cartItems = \App\Models\Cart::where('user_id', $user->id)
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
                fn($item) => $item->product ? $item->product->price * (int)$item->quantity : 0
            );

            // ✅ Shipping — compute ONLY if delivery
            $fulfillment = $request->input('fulfillment_method', 'delivery'); // <—
            $totalShipping = 0;
            if ($fulfillment === 'delivery') {
                // Determine buyer city from address_id (if provided), else fallback
                $buyerCity = null;
                if ($request->filled('address_id')) {
                    $addr = \App\Models\Address::where('id', $request->address_id)
                        ->where('user_id', $user->id)
                        ->first();
                    $buyerCity = optional($addr)->city;
                }
                $buyerCity = $buyerCity ?? ($user->city ?? $user->town ?? '');

                $shippingBySeller = [];
                foreach ($cartItems as $item) {
                    if (!$item->product || !$item->product->user) continue;

                    $sellerUser = $item->product->user;   // has seller loaded
                    $seller     = $sellerUser->seller;
                    $sellerCity = optional($seller)->pickup_city
                            ?? ($sellerUser->city ?? $sellerUser->town ?? '');

                    $sid = $sellerUser->id;

                    if (!isset($shippingBySeller[$sid])) {
                        $shippingBySeller[$sid] = [
                            'weight'      => 0,
                            'buyer_city'  => $buyerCity,
                            'seller_city' => $sellerCity,
                            'fee'         => 0,
                        ];
                    }

                    $shippingBySeller[$sid]['weight'] += ($item->product->weight ?? 0) * (int)$item->quantity;

                    $shippingBySeller[$sid]['fee'] = \App\Helpers\ShippingHelper::calculate(
                        $shippingBySeller[$sid]['buyer_city'],
                        $shippingBySeller[$sid]['seller_city'],
                        $shippingBySeller[$sid]['weight']
                    );
                }

                $totalShipping = array_sum(array_column($shippingBySeller, 'fee'));
            }

            // ✅ Grand total
            $grandTotal = $subtotal + $totalShipping;

            // ✅ Create Order (save the method; if pickup → address is null)
            $order = \App\Models\Order::create([
                'user_id'            => $user->id,
                'address_id'         => $fulfillment === 'delivery' ? $request->address_id : null,
                'payment_method'     => $request->payment_method,
                'fulfillment_method' => $fulfillment,                                              
                'status'             => 'pending',
                'total_amount'       => $grandTotal,
                'shipping_fee'       => $totalShipping,
            ]);

            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity'   => (int)$cartItem->quantity,
                    'price'      => $cartItem->product->price
                ]);

                if ($cartItem->id != 0) {
                    $cartItem->delete();
                }
            }

            $order->load('orderItems.product.user');

    // Group order items by seller_id
    $itemsBySeller = $order->orderItems
        ->filter(fn ($oi) => optional($oi->product)->user_id)
        ->groupBy(fn ($oi) => $oi->product->user_id);

    // Send ONE notification per seller with ONLY their items
    foreach ($itemsBySeller as $sellerId => $itemsForSeller) {
        $seller = User::find($sellerId);
        if (!$seller) continue;

        Notification::send($seller, new \App\Notifications\NewOrderForSeller($order, $itemsForSeller));
        // or: $seller->notify(new NewOrderForSeller($order, $itemsForSeller));
    }

    // Then finish as you do now:
    return response()->json([
        'success'      => true,
        'message'      => 'Order placed successfully!',
        'redirect_url' => route('checkout.success')
    ]);
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
