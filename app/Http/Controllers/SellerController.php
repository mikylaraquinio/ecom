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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


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
        'shop_name'            => 'required|string|max:30',
        'pickup_address'       => 'nullable|string|max:255',
        'pickup_full_name'     => 'nullable|string|max:255',
        'pickup_phone'         => 'nullable|string|max:50',
        'pickup_region_group'  => 'nullable|string|max:100',
        'pickup_province'      => 'nullable|string|max:100',
        'pickup_city'          => 'nullable|string|max:100',
        'pickup_barangay'      => 'nullable|string|max:100',
        'pickup_postal'        => 'nullable|string|max:16',
        'pickup_detail'        => 'nullable|string|max:1000',
        'business_type'        => 'required|string|in:individual,sole,corporation,cooperative',
        'tax_id'               => 'nullable|string|max:50',
        'gov_id'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        'rsbsa'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        'mayors_permit'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
    ]);

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
        'shop_name'           => $validated['shop_name'],
        'pickup_address'      => $validated['pickup_address']      ?? null,
        'pickup_full_name'    => $validated['pickup_full_name']    ?? null,
        'pickup_phone'        => $validated['pickup_phone']        ?? null,
        'pickup_region_group' => $validated['pickup_region_group'] ?? null,
        'pickup_province'     => $validated['pickup_province']     ?? null,
        'pickup_city'         => $validated['pickup_city']         ?? null,
        'pickup_barangay'     => $validated['pickup_barangay']     ?? null,
        'pickup_postal'       => $validated['pickup_postal']       ?? null,
        'pickup_detail'       => $validated['pickup_detail']       ?? null,

        'business_type'       => $validated['business_type'],
        'tax_id'              => $validated['tax_id']              ?? null,
        'gov_id_path'         => $govPath,
        'rsbsa_path'          => $rsbsaPath,
        'mayors_permit_path'  => $mayorsPermitPath,

        'status'              => 'approved', // or 'pending'
    ];

    \App\Models\Seller::updateOrCreate(['user_id' => $user->id], $data);

    if ($user->role !== 'seller') {
        $user->role = 'seller';
        $user->save();
        Auth::setUser($user->fresh());
    }

    // 👇 keep this route in sync with your JS (or change to user_profile if you prefer)
    $redirect = route('user_profile');


    if ($request->expectsJson()) {
        return response()->json([
            'success'      => true,
            'message'      => 'Seller registration saved!',
            'redirect_url' => $redirect,
        ], 200);
    }

    return redirect($redirect)->with('success', 'Seller registration saved!');
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

        // ===== STOCK HANDLING (when completed / picked up) =====
        if (in_array($newStatus, ['completed', 'picked_up'])) {
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

        // ===== TIMESTAMP UPDATES =====
        switch ($newStatus) {
            case 'accepted':
                $order->accepted_at = now();
                break;

            case 'ready_for_pickup':
                $order->ready_at = now();
                break;

            case 'picked_up':
            case 'completed':
                $order->delivered_at = now();
                break;

            case 'shipped':
                $order->shipped_at = now();
                break;
        }

        // ===== STATUS UPDATE =====
        $order->status = $newStatus;
        $order->save();

        // ===== NOTIFY BUYER =====
        $extra = null;
        if ($newStatus === 'shipped') {
            $extra = trim(' ' . ($request->courier ?? '') . ' ' . ($request->tracking_no ?? ''));
        }

        $this->notifyBuyer($order, $extra);

        return redirect()->route('myshop')->with('success', 'Order status updated successfully.');
    }

    public function generateInvoice($id)
    {
        $order = Order::with('orderItems.product', 'user', 'address')->findOrFail($id);

        // ✅ Security check
        if (auth()->user()->role !== 'seller') {
            abort(403, 'Unauthorized');
        }

        // === 🧾 Case 1: Online Payment (Generate seller's own PDF) ===
        if ($order->payment_method === 'online') {
            // Generate internal seller invoice (PDF)
            $pdf = Pdf::loadView('invoices.seller_invoice', compact('order'))
                    ->setPaper('a4', 'portrait');

            $fileName = 'seller_invoice_' . $order->id . '.pdf';
            $path = 'invoices/' . $fileName;

            Storage::disk('public')->put($path, $pdf->output());

            // Save invoice record to DB
            $order->seller_invoice_url = asset('storage/' . $path);
            $order->invoice_generated = true;
            $order->save();

            return redirect()->route('seller.viewInvoice', $order->id)
                ->with('success', 'Seller E-Invoice generated successfully.');
        }

        // === 🧾 Case 2: COD Payment (Manual Generate) ===
        if ($order->payment_method === 'cod') {
            $pdf = Pdf::loadView('invoices.cod_invoice', compact('order'))
                    ->setPaper('a4', 'portrait');

            $fileName = 'cod_invoice_' . $order->id . '.pdf';
            $path = 'invoices/' . $fileName;

            Storage::disk('public')->put($path, $pdf->output());

            $order->invoice_url = asset('storage/' . $path);
            $order->invoice_generated = true;
            $order->save();

            return redirect()->route('seller.viewInvoice', $order->id)
                ->with('success', 'COD E-Invoice generated successfully.');
        }

        return back()->with('error', 'Unsupported payment method.');
    }

public function viewInvoice($id)
{
    $order = Order::with('orderItems.product', 'user', 'address')->findOrFail($id);

    // Only buyer or seller should access
    if (!in_array(auth()->user()->role, ['buyer', 'seller', 'admin'])) {
        abort(403, 'Unauthorized');
    }

    // If it's Xendit, redirect to Xendit invoice link
    if ($order->payment_method === 'online' && $order->invoice_url) {
        return redirect($order->invoice_url);
    }

    // If COD, show your custom invoice page
    if ($order->payment_method === 'cod' && $order->invoice_generated) {
        return view('invoices.cod_invoice', compact('order'));
    }

    return back()->with('error', 'No invoice available.');
}

    public function myShop()
    {
        $categories = Category::all();
        return view('myshop', compact('categories'));
    }

    public function index(Request $request)
    {$user = auth()->user();
$mainCategories = \App\Models\Category::whereNull('parent_id')->get();

// ✅ Safely handle guests (null user)
$unreadNotifications = collect();
$allNotifications = collect();

if ($user) {
    $unreadNotifications = $user->unreadNotifications()->latest()->take(10)->get();
    $allNotifications = $user->notifications()->latest()->paginate(10);
}


        // ✅ Fetch seller's orders with optional status filter
        $orders = Order::with(['buyer', 'orderItems.product', 'shippingAddress'])
            ->whereHas('orderItems.product', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        // ✅ Order Status Counts (for the 4 summary boxes)
        $orderCounts = [
            'pending' => Order::whereHas('orderItems.product', fn($q) => $q->where('user_id', $user->id))
                            ->where('status', 'pending')->count(),
            'canceled' => Order::whereHas('orderItems.product', fn($q) => $q->where('user_id', $user->id))
                            ->where('status', 'canceled')->count(),
            'denied' => Order::whereHas('orderItems.product', fn($q) => $q->where('user_id', $user->id))
                            ->where('status', 'denied')->count(),
            'completed' => Order::whereHas('orderItems.product', fn($q) => $q->where('user_id', $user->id))
                            ->where('status', 'completed')->count(),
        ];

        $products = $user->products;

        // ✅ Pass $orderCounts to the view
        return view('myshop', compact(
            'user',
            'products',
            'mainCategories',
            'orders',
            'orderCounts',
            'unreadNotifications',
            'allNotifications'
        ));
    }

    public function analytics()
{
    $sellerId = auth()->id();

    // 🧾 Fetch all orders related to the seller's products
    $orders = Order::whereHas('orderItems.product', fn($q) => $q->where('user_id', $sellerId))->get();

    // ===== BASIC METRICS =====
    $completedSales = $orders->where('status', 'completed')->sum('total_amount');
    $pendingSales   = $orders->where('status', 'pending')->sum('total_amount');
    $totalSales     = $completedSales + $pendingSales;
    $totalOrders    = $orders->count();

    $completedOrders = $orders->where('status', 'completed')->count();
    $pendingOrders   = $orders->where('status', 'pending')->count();
    $acceptedOrders  = $orders->where('status', 'accepted')->count();
    $shippedOrders   = $orders->where('status', 'shipped')->count();
    $canceledOrders  = $orders->where('status', 'canceled')->count();

    $deliveryOrders  = $orders->where('fulfillment_method', 'delivery')->count();
    $pickupOrders    = $orders->where('fulfillment_method', 'pickup')->count();

    $uniqueCustomers = $orders->pluck('user_id')->unique()->count();
    $repeatCustomers = $orders->groupBy('user_id')->filter(fn($g) => $g->count() > 1)->count();
    $avgOrderValue   = $completedOrders ? $completedSales / $completedOrders : 0;

    // ===== REVENUE BY MONTH =====
    $monthlyRevenue = $orders->where('status', 'completed')
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->sum('total_amount');

    // ===== REVENUE TREND =====
    $salesTrends = $orders->where('status', 'completed')
        ->groupBy(fn($order) => $order->created_at->format('M'))
        ->map->sum('total_amount')
        ->toArray();

    // ===== TOP SELLING PRODUCTS =====
    $topProducts = \App\Models\OrderItem::whereHas('product', fn($q) => $q->where('user_id', $sellerId))
        ->selectRaw('product_id, SUM(quantity) as total_quantity')
        ->groupBy('product_id')
        ->with('product')
        ->orderByDesc('total_quantity')
        ->take(5)
        ->get();

    // ✅ Fix: define $mostSoldProduct
    $mostSoldProduct = $topProducts->first();

    // ===== LOW STOCK PRODUCTS =====
    $lowStockProducts = \App\Models\Product::where('user_id', $sellerId)
        ->where('stock', '<=', 5)
        ->get();

    // ===== RECENT ORDERS =====
    $recentOrders = Order::whereHas('orderItems.product', fn($q) => 
        $q->where('user_id', $sellerId)
    )->latest()->take(5)->get();

    // ===== RETURN VIEW =====
    return view('seller.analytics', compact(
        'completedSales',
        'pendingSales',
        'totalSales',
        'totalOrders',
        'completedOrders',
        'pendingOrders',
        'acceptedOrders',
        'shippedOrders',
        'canceledOrders',
        'deliveryOrders',
        'pickupOrders',
        'uniqueCustomers',
        'repeatCustomers',
        'avgOrderValue',
        'monthlyRevenue',
        'salesTrends',
        'topProducts',
        'mostSoldProduct',  // ✅ Added
        'lowStockProducts',
        'recentOrders'      // ✅ Added
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
