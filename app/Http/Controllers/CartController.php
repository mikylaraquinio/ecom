<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view your cart.');
        }

        $cartItems = Cart::where('user_id', $user->id)
            ->with('product')
            ->get();

        return view('cart', compact('cartItems'));
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
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop')->with('error', 'No valid items found for checkout.');
        }

        $totalPrice = 0;
        $orderItems = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            if ($product->stock < $cartItem->quantity) {
                return redirect()->route('cart.index')->with('error', "Not enough stock available for {$product->name}.");
            }

            $totalPrice += $product->price * $cartItem->quantity;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'price' => $product->price,
            ];

            // Reduce stock quantity
            $product->stock -= $cartItem->quantity;
            $product->save();
        }

        $order = Order::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
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
