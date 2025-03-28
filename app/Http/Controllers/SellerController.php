<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Seller;

class SellerController extends Controller
{
    public function sell()
    {
        return view('farmers.modal.sell');
    }

    public function storeSeller(Request $request)
    {
        $request->validate([
            'farm_name' => 'required|string|max:255',
            'farm_address' => 'required|string|max:255',
            'gov_id' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'farm_certificate' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'mobile_money' => 'nullable|string|max:20',
            'terms' => 'required|accepted',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found or not authenticated.');
        }

        // Handle file uploads
        $govIdPath = $request->hasFile('gov_id') ? $request->file('gov_id')->store('documents', 'public') : $user->gov_id;
        $farmCertPath = $request->hasFile('farm_certificate') ? $request->file('farm_certificate')->store('documents', 'public') : $user->farm_certificate;

        // Update user details
        $user->update([
            'farm_name' => $request->farm_name,
            'farm_address' => $request->farm_address,
            'gov_id' => $govIdPath,
            'farm_certificate' => $farmCertPath,
            'mobile_money' => $request->mobile_money,
            'role' => 'seller', // Change role to seller
        ]);

        return redirect()->route('user_profile')->with('success', 'Seller registration successful!');
    }

    public function myOrders()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with('orderItems.product') // FIXED: Changed 'items' to 'orderItems'
            ->orderBy('created_at', 'desc')
            ->get();

        return view('buyer.orders', compact('orders'));
    }

    public function incomingOrders()
    {
        $orders = Order::whereHas('orderItems', function ($query) {
            $query->whereHas('product', function ($subQuery) {
                $subQuery->where('user_id', auth()->id());
            });
        })->get();

        $user = auth()->user();
        $mainCategories = Category::whereNull('parent_id')->get(); // Add this

        return view('myshop', compact('orders', 'mainCategories', 'user'));
    }

    public function approveOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'accepted']);

        return redirect()->route('myshop')->with('success', 'Order approved successfully!');
    }

    public function denyOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'denied']);

        return redirect()->route('myshop')->with('error', 'Order denied!');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return redirect()->route('myshop')->with('success', 'Order status updated successfully!');
    }

    public function myShop()
    {
        $categories = Category::all();
        return view('myshop', compact('categories'));
    }

    public function index()
    {
        $user = auth()->user();

        // Fetch main categories
        $mainCategories = Category::whereNull('parent_id')->get();

        // Fetch seller's orders
        $orders = [];
        if ($user->role === 'seller') {
            $orders = Order::whereHas('orderItems.product', function ($query) use ($user) {
                $query->where('user_id', $user->id); // FIXED: Changed 'items' to 'orderItems'
            })->with('orderItems.product')->orderBy('created_at', 'desc')->get(); // FIXED: Changed 'items' to 'orderItems'
        }

        $products = $user->products;

        return view('myshop', compact('user', 'products', 'mainCategories', 'orders'));
    }

    public function confirmReceipt($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $order->update(['status' => 'received']);

        return redirect()->route('buyer.orders')->with('success', 'Order received successfully!');
    }
}
