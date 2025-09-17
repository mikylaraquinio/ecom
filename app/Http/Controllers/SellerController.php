<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

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

        // Get authenticated user
        $user = Auth::user();

        if (!$user || !$user instanceof User) {
            return redirect()->back()->with('error', 'User not found or not authenticated.');
        }

        // Handle file uploads
        $govIdPath = $request->hasFile('gov_id') ? $request->file('gov_id')->store('documents', 'public') : $user->gov_id;
        $farmCertPath = $request->hasFile('farm_certificate') ? $request->file('farm_certificate')->store('documents', 'public') : $user->farm_certificate;

        // Manually updating attributes instead of using update()
        $user->farm_name = $request->farm_name;
        $user->farm_address = $request->farm_address;
        $user->gov_id = $govIdPath;
        $user->farm_certificate = $farmCertPath;
        $user->mobile_money = $request->mobile_money;
        $user->role = 'seller'; // Change user role to seller

        $user->save(); // Save changes to the database

        return redirect()->route('user_profile')->with('success', 'Seller registration successful!');
    }

    public function myOrders()
    {
        $user = auth()->user();

        $ordersToShip = $user->orders()
            ->whereIn('status', ['pending', 'accepted'])
            ->with('orderItems.product.seller')
            ->get();

        $ordersToReceive = $user->orders()
            ->where('status', 'shipped')
            ->with('orderItems.product.seller')
            ->get();

        $ordersToReview = $user->orders()
            ->where('status', 'completed')
            ->with('orderItems.product.seller')
            ->get();

        $wishlistItems = $user->wishlist()->with('seller')->get();

        return view('user_profile', compact(
            'ordersToShip',
            'ordersToReceive',
            'ordersToReview',
            'wishlistItems'
        ));
    }


    public function incomingOrders()
    {
        $orders = Order::whereHas('orderItems', function ($query) {
            $query->whereHas('product', function ($subQuery) {
                $subQuery->where('user_id', auth()->id());
            });
        })->orderBy('created_at', 'desc') // ✅ Add this
            ->with('orderItems.product', 'buyer', 'shippingAddress') // Optional: eager load to reduce N+1
            ->get();

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
        $newStatus = $request->input('status');

        // When marking as completed, reduce stock
        if ($newStatus === 'completed') {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                if ($product->stock >= $item->quantity) {
                    $product->stock -= $item->quantity;
                    $product->save();
                } else {
                    return redirect()->back()->with('error', "Not enough stock for {$product->name}.");
                }
            }
            $order->delivered_at = now(); // ✅ Set delivered date
        }

        if ($newStatus === 'shipped') {
            $order->shipped_at = now(); // ✅ Set shipped date
        }

        $order->status = $newStatus;
        $order->save();

        return redirect()->route('myshop')->with('success', 'Order status updated successfully.');
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
                $query->where('user_id', $user->id);
            })->with('orderItems.product')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        $products = $user->products;

        // ✅ Completed Sales (actual revenue)
        $completedSales = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'completed')
            ->sum('total_amount');

        // ✅ Pending Sales (potential revenue)
        $pendingSales = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')
            ->sum('total_amount');

        // ✅ Combine into total (optional)
        $totalSales = $completedSales + $pendingSales;

        // ✅ Total Orders (all statuses)
        $totalOrders = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        // ✅ Completed Orders only
        $completedOrders = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'completed')->count();

        // ✅ Pending Orders only
        $pendingOrders = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->count();

        // Get all seller's completed order items grouped by product
        $topProducts = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'completed')
            ->with('orderItems.product')
            ->get()
            ->flatMap->orderItems
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'product' => $items->first()->product,
                    'total_quantity' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(5); // ✅ Top 5

        // Pick the first as the "Most Sold"
        $mostSoldProduct = $topProducts->first();

        $lowStockCount = $user->products()
            ->where('stock', '<=', 5)
            ->count();

        $lowStockProducts = $user->products()
            ->where('stock', '<=', 5)
            ->get();


        $revenueTrends = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'completed') // only count completed orders
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month');


        return view('myshop', compact(
            'user',
            'products',
            'mainCategories',
            'orders',
            'completedSales',
            'pendingSales',
            'totalSales',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'topProducts',
            'mostSoldProduct',
            'lowStockCount',
            'lowStockProducts',
            'revenueTrends'
        ));
    }

    public function confirmReceipt($id)
    {
        $order = Order::findOrFail($id);

        // Make sure only the buyer who owns the order can confirm
        if ($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Mark as completed (same as seller side)
        $order->status = 'completed';
        $order->delivered_at = now();
        $order->save();

        return redirect()->route('user_profile')->with('success', 'Order marked as completed.');
    }

    public function approveCancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'cancel_requested') {
            $order->update(['status' => 'canceled']);
            return redirect()->route('myshop')->with('success', 'Order has been successfully canceled.');
        }

        return redirect()->route('myshop')->with('error', 'Invalid request.');
    }

    public function denyCancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'cancel_requested') {
            $order->update(['status' => 'accepted']);
            return redirect()->route('myshop')->with('success', 'Cancelation denied. Order is still accepted.');
        }

        return redirect()->route('myshop')->with('error', 'Invalid request.');
    }

}
