<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    public function index()
    {
        return view('cart'); // Ensure 'cart.blade.php' exists in the 'resources/views/' folder
    }

    public function add(Request $request, $id)
    {
        $product = \App\Models\Product::find($id);
        if (!$product) {
            return redirect()->route('shop')->with('error', 'Product not found.');
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += 1;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart')->with('success', 'Product added to cart.');
    }

    public function update(Request $request, $id)
    {
        if ($request->quantity < 1) {
            return redirect()->route('cart')->with('error', 'Quantity must be at least 1.');
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Cart updated successfully.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Item removed from cart.');
    }


    /*Checkout*/
    public function process(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $cart = session('cart', []);

        if (!$cart || count($cart) == 0) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        // Create new order
        $order = new Order();
        $order->user_id = Auth::id();
        $order->name = $request->name;
        $order->address = $request->address;
        $order->payment_method = $request->payment_method;
        $order->total_price = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        $order->status = 'pending';
        $order->save();

        // Save order items
        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('shop')->with('success', 'Your order has been placed successfully!');
    }
    
    public function showUserProfile()
    {
        // Retrieve all orders for the authenticated user
        $orders = Order::where('user_id', Auth::id())->where('status', 'pending')->get();

        // Pass orders to the view
        return view('user_profile', compact('orders'));
    }

    public function cancel(Order $order)
    {
        if ($order->status == 'pending') {
            $order->status = 'canceled';
            $order->save();
            
            return redirect()->back()->with('success', 'Order has been canceled.');
        }

        return redirect()->back()->with('error', 'You cannot cancel this order.');
    }

    public function edit(Order $order)
    {
        // Check if the order is editable (only if status is 'pending')
        if ($order->status != 'pending') {
            return redirect()->route('user.profile')->with('error', 'You cannot edit this order.');
        }

        return view('order.edit', compact('order'));
    }
    


}

