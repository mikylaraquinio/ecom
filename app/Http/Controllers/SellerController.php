<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Notifications\OrderStatusUpdated;
use App\Models\Seller;


class SellerController extends Controller
{
    public function sell()
    {
        return view('farmers.modal.sell');
    }

    public function storeSeller(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'shop_name' => 'required|string|max:30',
            'pickup_address' => 'nullable|string|max:255',
            'pickup_full_name' => 'nullable|string|max:255',
            'pickup_phone' => 'nullable|string|max:50',
            'pickup_region_group' => 'nullable|string|max:100',
            'pickup_province' => 'nullable|string|max:100',
            'pickup_city' => 'nullable|string|max:100',
            'pickup_barangay' => 'nullable|string|max:100',
            'pickup_postal' => 'nullable|string|max:16',
            'pickup_detail' => 'nullable|string|max:1000',
            'business_type' => 'required|string|in:individual,sole,corporation,cooperative',
            'tax_id' => 'nullable|string|max:50',
            'gov_id' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'rsbsa' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'mayors_permit' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Handle document uploads
        $govPath = $request->file('gov_id')
            ? $request->file('gov_id')->store('seller_docs', 'public')
            : optional($user->seller)->gov_id_path;

        $rsbsaPath = $request->file('rsbsa')
            ? $request->file('rsbsa')->store('seller_docs', 'public')
            : optional($user->seller)->rsbsa_path;

        $mayorsPermitPath = $request->file('mayors_permit')
            ? $request->file('mayors_permit')->store('seller_docs', 'public')
            : optional($user->seller)->mayors_permit_path;

        $data = [
            'shop_name' => $validated['shop_name'],
            'pickup_address' => $validated['pickup_address'] ?? null,
            'pickup_full_name' => $validated['pickup_full_name'] ?? null,
            'pickup_phone' => $validated['pickup_phone'] ?? null,
            'pickup_region_group' => $validated['pickup_region_group'] ?? null,
            'pickup_province' => $validated['pickup_province'] ?? null,
            'pickup_city' => $validated['pickup_city'] ?? null,
            'pickup_barangay' => $validated['pickup_barangay'] ?? null,
            'pickup_postal' => $validated['pickup_postal'] ?? null,
            'pickup_detail' => $validated['pickup_detail'] ?? null,

            'business_type' => $validated['business_type'],
            'tax_id' => $validated['tax_id'] ?? null,
            'gov_id_path' => $govPath,
            'rsbsa_path' => $rsbsaPath,
            'mayors_permit_path' => $mayorsPermitPath,

            'status' => 'pending', // 👈 mark as pending until admin approves
        ];

        Seller::updateOrCreate(['user_id' => $user->id], $data);

        // 🚫 Don't set role to seller yet — wait for admin approval

        $redirect = route('user_profile');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Seller registration submitted for admin approval.',
                'redirect_url' => $redirect,
            ], 200);
        }

        return redirect($redirect)->with('success', 'Your seller registration has been submitted and is now pending admin approval.');
    }



    public function myOrders()
    {
        // ✅ fix: use auth()->user()
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
                // ✅ fix: use auth()->id()
                $subQuery->where('user_id', auth()->id());
            });
        })
            ->orderBy('created_at', 'desc')
            ->with('orderItems.product', 'buyer', 'shippingAddress')
            ->get();

        // ✅ fix: use auth()->user()
        $user = auth()->user();
        $mainCategories = Category::whereNull('parent_id')->get();

        // 🔔 Notifications for this page as well (optional but useful)
        $unreadNotifications = $user->unreadNotifications()->latest()->take(10)->get();
        $allNotifications = $user->notifications()->latest()->paginate(10);

        // (Optional) Mark order notifications as read when viewing this page:
        // $user->unreadNotifications()
        //     ->where('type', \App\Notifications\NewIncomingOrderNotification::class)
        //     ->get()->each->markAsRead();

        return view('myshop', compact(
            'orders',
            'mainCategories',
            'user',
            'unreadNotifications',
            'allNotifications'
        ));
    }

    public function approveOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

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
            $order->delivered_at = now();
        }

        if ($newStatus === 'shipped') {
            $order->shipped_at = now();
        }

        $order->status = $newStatus;
        $order->save();

        $extra = null;
        if ($newStatus === 'shipped') {
            $extra = trim(' ' . ($request->courier ?? '') . ' ' . ($request->tracking_no ?? ''));
        }
        $this->notifyBuyer($order, $extra); // 👈

        return redirect()->route('myshop')->with('success', 'Order status updated successfully.');
    }

    public function myShop()
    {
        $categories = Category::all();
        return view('myshop', compact('categories'));
    }

    public function index(Request $request)
    {
        // ✅ fix: use auth()->user()
        $user = auth()->user();

        // Fetch main categories
        $mainCategories = Category::whereNull('parent_id')->get();

        // 🔔 Notifications (Step 6)
        $unreadNotifications = $user->unreadNotifications()->latest()->take(10)->get();
        $allNotifications = $user->notifications()->latest()->paginate(10);

        // Fetch seller's orders with optional status filter
        $orders = collect();
        if ($user->role === 'seller') {
            $orders = Order::whereHas('orderItems.product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->when($request->filled('status'), function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->with([
                    'orderItems.product',   // products in the order
                    'buyer',                // used as $order->buyer->name
                    'shippingAddress',      // used in the table
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends($request->query()); // keep filter in pagination links
        }

        $products = $user->products;

        // --- analytics ---
        $completedSales = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'completed')->sum('total_amount');

        $pendingSales = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->sum('total_amount');

        $totalSales = $completedSales + $pendingSales;

        $totalOrders = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $completedOrders = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'completed')->count();

        $pendingOrders = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->count();

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
            ->take(5);

        $mostSoldProduct = $topProducts->first();

        $lowStockCount = $user->products()->where('stock', '<=', 5)->count();
        $lowStockProducts = $user->products()->where('stock', '<=', 5)->get();

        $revenueTrends = Order::whereHas('orderItems.product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'completed')
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
            'revenueTrends',
            'unreadNotifications',
            'allNotifications',
            'products'
        ));
    }

    public function revenueData(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'monthly'); // default monthly

        $query = Order::whereHas('orderItems.product', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'completed');

        switch ($type) {
            case 'daily':
                $data = $query->select(
                    DB::raw('DATE(updated_at) as label'),
                    DB::raw('SUM(total_amount) as total')
                )
                    ->groupBy(DB::raw('DATE(updated_at)'))
                    ->orderBy('label')
                    ->pluck('total', 'label');
                break;

            case 'weekly':
                $data = $query->select(
                    DB::raw('YEARWEEK(updated_at) as label'),
                    DB::raw('SUM(total_amount) as total')
                )
                    ->groupBy(DB::raw('YEARWEEK(updated_at)'))
                    ->orderBy('label')
                    ->pluck('total', 'label');
                break;

            case 'monthly':
                $data = $query->select(
                    DB::raw('YEAR(updated_at) as year'),
                    DB::raw('MONTH(updated_at) as month'),
                    DB::raw('SUM(total_amount) as total')
                )
                    ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->mapWithKeys(fn($row) => [
                        $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT) => $row->total
                    ]);
                break;

            case 'quarterly':
                $data = $query->select(
                    DB::raw('YEAR(updated_at) as year'),
                    DB::raw('QUARTER(updated_at) as quarter'),
                    DB::raw('SUM(total_amount) as total')
                )
                    ->groupBy(DB::raw('YEAR(updated_at), QUARTER(updated_at)'))
                    ->orderBy('year')
                    ->orderBy('quarter')
                    ->get()
                    ->mapWithKeys(fn($row) => [
                        $row->year . ' Q' . $row->quarter => $row->total
                    ]);
                break;

            case 'yearly':
                $data = $query->select(
                    DB::raw('YEAR(updated_at) as label'),
                    DB::raw('SUM(total_amount) as total')
                )
                    ->groupBy(DB::raw('YEAR(updated_at)'))
                    ->orderBy('label')
                    ->pluck('total', 'label');
                break;
        }

        return response()->json($data);
    }

    public function confirmReceipt($id)
    {
        $order = Order::findOrFail($id);

        // ✅ fix: use auth()->id()
        if ($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

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

    private function notifyBuyer(Order $order, ?string $extraMsg = null): void
    {
        $buyer = $order->user ?? $order->buyer;
        if ($buyer) {
            $buyer->notify(new OrderStatusUpdated($order, $extraMsg));
        }
    }



}
