<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use App\Helpers\ShippingHelper;
use App\Models\Seller;


class CartController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ✅ Get all cart items with product, user, and seller
        $cartItems = Cart::with(['product.user.seller', 'product.category'])
            ->where('user_id', $user->id)
            ->get();

        // ✅ Get user's address
        $address = Address::where('user_id', $user->id)->first();

        $shippingBySeller = [];
        $totalShipping = 0;
        $subtotal = 0;

        if ($cartItems->count() > 0) {
            $groups = $cartItems->filter(fn($i) => $i->product)
                ->groupBy(fn($i) => $i->product->user_id);

            foreach ($groups as $sellerId => $items) {
                $sellerSubtotal = 0;
                $weight = 0;
                $hasLivestock = false;

                foreach ($items as $item) {
                    $product = $item->product;
                    $sellerSubtotal += $product->price * $item->quantity;
                    $weight += ($product->weight ?? 0) * $item->quantity;

                    // ✅ detect livestock
                    if ($product->category && $product->category->name === 'Livestock') {
                        $hasLivestock = true;
                    }
                }

                $subtotal += $sellerSubtotal;

                $shippingFee = 0;

                if ($address) {
                    $seller = $items->first()->product->user->seller ?? null;
                    $sellerCity = $seller?->pickup_city ?? null;
                    $buyerCity = $address->city ?? null;

                    \Log::info('Seller/Buyer debug', [
                        'sellerId' => $sellerId,
                        'sellerCity' => $sellerCity,
                        'buyerCity' => $buyerCity,
                        'sellerShop' => $seller?->shop_name ?? 'no seller',
                        'buyerName' => $user->name,
                    ]);


                    // ✅ Only calculate if both buyer & seller cities exist
                    if ($sellerCity && $buyerCity) {
                        $shippingFee = ShippingHelper::calculate(
                            $buyerCity,
                            $sellerCity,
                            $weight,
                            $hasLivestock,
                            $address->address ?? null,
                            $seller->pickup_address ?? null
                        );
                    } else {
                        // fallback if missing
                        $shippingFee = ShippingHelper::calculate(null, null, $weight);
                    }
                }

                $shippingBySeller[$sellerId] = [
                    'shippingFee' => $shippingFee,
                    'weight' => $weight,
                ];

                $totalShipping += $shippingFee;
            }
        }

        $grandTotal = $subtotal + $totalShipping;
        \Log::info("Cart Shipping Debug", compact('totalShipping', 'shippingBySeller', 'subtotal', 'grandTotal'));

        return view('cart', compact('cartItems', 'shippingBySeller', 'totalShipping', 'grandTotal'));
    }




    public function add(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'You must be logged in to add to cart.']);
        }

        // Check if the requested quantity is available
        if ($request->quantity > $product->stock) {
            return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
        }

        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            // Ensure new quantity does not exceed stock
            if ($newQuantity > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
            }

            $cartItem->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $id,
                'quantity' => $request->quantity
            ]);
        }

        // Fetch updated cart items
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart!',
            'cart' => $cartItems
        ]);
    }

    public function update(Request $request, $id)
    {
        $cartItem = Cart::find($id);
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found.']);
        }

        $newQuantity = (int) $request->quantity;
        $productStock = $cartItem->product->stock; // Assuming 'stock' is a column in the products table

        if ($newQuantity > $productStock) {
            return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'new_quantity' => $cartItem->quantity,
            'remaining_stock' => $productStock - $cartItem->quantity
        ]);
    }

    public function remove($id)
    {
        $cartItem = Cart::find($id);
        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Item not found.']);
        }
    }

    public function bulkDelete(Request $request)
    {
        $itemIds = $request->input('selected_items', []);
        if (!empty($itemIds)) {
            Cart::whereIn('id', $itemIds)->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }
    }

    public function shipping(Request $request)
    {
        $user = Auth::user();
        $items = $request->input('items', []); // selected cart items

        $perSeller = [];
        foreach ($items as $item) {
            $cartItem = Cart::with('product.user')->where('user_id', $user->id)->find($item['id']);
            if (!$cartItem || !$cartItem->product)
                continue;

            $qty = (int) $item['qty'];
            $product = $cartItem->product;
            $sellerId = $product->user_id;

            $address = Address::where('user_id', $user->id)->first();
            $seller = $product->user->seller ?? null;

            $buyerTown = $address?->city ?? 'default';
            $sellerTown = $seller?->pickup_city ?? 'default';

            if (!isset($perSeller[$sellerId])) {
                $perSeller[$sellerId] = [
                    'weight' => 0,
                    'fee' => 0,
                ];
            }

            // accumulate weight
            $perSeller[$sellerId]['weight'] += ($product->weight ?? 0) * $qty;

            // calculate fee for this seller’s total weight
            $perSeller[$sellerId]['fee'] = \App\Helpers\ShippingHelper::calculate(
                $buyerTown,
                $sellerTown,
                $perSeller[$sellerId]['weight']
            );



            if (!isset($perSeller[$sellerId])) {
                $perSeller[$sellerId] = [
                    'weight' => 0,
                    'fee' => 0,
                ];
            }

            // accumulate weight
            $perSeller[$sellerId]['weight'] += ($product->weight ?? 0) * $qty;

            // calculate fee for this seller’s total weight
            $perSeller[$sellerId]['fee'] = \App\Helpers\ShippingHelper::calculate(
                $buyerTown,
                $sellerTown,
                $perSeller[$sellerId]['weight']
            );
        }

        $totalFee = array_sum(array_column($perSeller, 'fee'));

        return response()->json([
            'totalShipping' => $totalFee,
            'perSeller' => $perSeller, // 👈 match frontend
        ]);
    }



    public function checkoutSelected(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);
        $user = Auth::user();

        $checkoutItems = Cart::where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->with('product')
            ->get();

        return view('checkout', compact('checkoutItems'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'selected_items' => 'required|array',
        ]);

        $user = Auth::user();
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
        }

        $cartItems = Cart::where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->with(['product.user', 'product.category'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop')->with('error', 'No valid items found for checkout.');
        }

        $totalPrice = 0;
        $totalWeightPerSeller = [];
        $orderItems = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            if ($product->stock < $cartItem->quantity) {
                return redirect()->route('cart.index')->with('error', "Not enough stock available for {$product->name}.");
            }

            $totalPrice += $product->price * $cartItem->quantity;

            $seller = $product->user;
            $sellerId = $seller->id;
            $buyerTown = $user->town ?? 'default_town';
            $sellerTown = $seller->town ?? 'default_town';

            if (!isset($totalWeightPerSeller[$sellerId])) {
                $totalWeightPerSeller[$sellerId] = [
                    'buyerTown' => $buyerTown,
                    'sellerTown' => $sellerTown,
                    'weight' => 0,
                    'hasLivestock' => false,
                    'seller' => $seller,
                ];
            }

            // ✅ detect livestock
            if ($product->category && $product->category->name === 'Livestock') {
                $totalWeightPerSeller[$sellerId]['hasLivestock'] = true;
            }

            $totalWeightPerSeller[$sellerId]['weight'] += ($product->weight ?? 0) * $cartItem->quantity;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'price' => $product->price,
            ];

            $product->stock -= $cartItem->quantity;
            $product->save();
        }

        // ✅ calculate shipping
        $shippingFee = 0;
        foreach ($totalWeightPerSeller as $info) {
            $shippingFee += ShippingHelper::calculate(
                $info['buyerTown'],
                $info['sellerTown'],
                $info['weight'],
                $info['hasLivestock'],
                $user->address ?? null,
                $info['seller']->farm_address ?? null
            );
        }

        $grandTotal = $totalPrice + $shippingFee;

        $order = Order::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'total_price' => $grandTotal,
            'shipping_fee' => $shippingFee,
            'status' => 'pending',
        ]);

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        Cart::whereIn('id', $selectedItems)->delete();

        return redirect()->route('shop')->with('success', 'Your order has been placed successfully!');
    }


    public function showUserProfile()
    {
        $orders = Order::where('user_id', Auth::id())->where('status', 'pending')->get();

        return view('user_profile', compact('orders'));
    }

    public function cancel(Order $order)
    {
        if ($order->status == 'pending') {
            $order->update(['status' => 'canceled']);
            return redirect()->back()->with('success', 'Order has been canceled.');
        }

        return redirect()->back()->with('error', 'You cannot cancel this order.');
    }

    public function edit(Order $order)
    {
        if ($order->status != 'pending') {
            return redirect()->route('user.profile')->with('error', 'You cannot edit this order.');
        }

        return view('order.edit', compact('order'));
    }
}
