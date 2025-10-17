<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderForSeller;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class CheckoutController extends Controller
{
    /**
     * Prepare checkout (from cart or Buy Now)
     */
    public function prepareCheckout(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);

        // From cart
        if (!empty($selectedItems)) {
            session(['selected_items' => $selectedItems]);
            return response()->json(['redirect_url' => route('checkout.show')]);
        }

        // From Buy Now
        if ($request->has('product_id')) {
            $product = Product::findOrFail($request->product_id);
            $quantity = max(1, (int)$request->input('quantity', 1));

            if ($product->stock < $quantity) {
                return redirect()->back()->with('error', 'Not enough stock available.');
            }

            session([
                'selected_items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'buy_now' => true,
                    ]
                ]
            ]);

            return redirect()->route('checkout.show');
        }

        return response()->json(['error' => 'No items selected.'], 400);
    }

    /**
     * Show checkout page
     */
    public function showCheckout()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $selectedItems = session('selected_items', []);
        if (empty($selectedItems)) {
            return redirect()->route('shop')->with('error', 'No items selected for checkout.');
        }

        $cartItems = collect();

        // --- BUY NOW ---
        if (isset($selectedItems[0]['product_id'])) {
            foreach ($selectedItems as $item) {
                $product = Product::with('user.seller')->findOrFail($item['product_id']);
                $fake = new Cart([
                    'id' => 0,
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => (int)$item['quantity'],
                ]);
                $fake->setRelation('product', $product);
                $cartItems->push($fake);
            }
        } 
        // --- CART CHECKOUT ---
        else {
            $cartItems = Cart::where('user_id', $user->id)
                ->whereIn('id', $selectedItems)
                ->with('product.user.seller')
                ->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        // --- SUBTOTAL ---
        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        // --- ADDRESS ---
        $addresses = $user->addresses;
        $selectedAddressId = session('selected_address_id');
        $selectedAddr = $addresses->firstWhere('id', $selectedAddressId) ?? $addresses->first();
        $buyerCity = optional($selectedAddr)->city ?? $user->city ?? '';

        // --- SHIPPING (mock ₱50 per seller) ---
        $shippingBySeller = [];
        $shippingFeePerSeller = 50;
        $uniqueSellers = $cartItems->pluck('product.user_id')->unique()->count();
        $totalShipping = $uniqueSellers * $shippingFeePerSeller;

        // --- Pickup Addresses by Seller ---
        $pickupBySeller = [];
        foreach ($cartItems as $item) {
            $seller = optional($item->product)->user;
            if (!$seller) continue;
            $sid = $seller->id;
            if (isset($pickupBySeller[$sid])) continue;

            $sp = optional($seller->seller);
            $addressLine = $sp?->pickup_address
                ?: collect([
                    $sp?->pickup_detail,
                    $sp?->pickup_barangay,
                    $sp?->pickup_city,
                    $sp?->pickup_province,
                ])->filter()->implode(', ');

            if (empty($addressLine)) {
                $addressLine = collect([
                    $seller->barangay,
                    $seller->city ?? $seller->town,
                    $seller->province,
                ])->filter()->implode(', ');
            }

            if (empty($addressLine)) {
                $addressLine = $seller->name;
            }

            $pickupBySeller[$sid] = [
                'name' => $seller->name,
                'phone' => $sp?->pickup_phone ?? $seller->phone ?? '',
                'address_line' => $addressLine,
            ];
        }

        $grandTotal = $subtotal + $totalShipping;

        return view('checkout', compact(
            'user',
            'cartItems',
            'addresses',
            'subtotal',
            'totalShipping',
            'grandTotal',
            'shippingBySeller',
            'pickupBySeller'
        ));
    }

    /**
     * Process checkout
     */
    public function process(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'payment_method'     => 'required|in:online,cod',
            'fulfillment_method' => 'required|in:delivery,pickup',
            'address_id'         => 'nullable|required_if:fulfillment_method,delivery|exists:addresses,id',
        ]);

        $user = Auth::user();
        $selectedItems = $request->selected_items ?? session('selected_items', []);
        if (empty($selectedItems)) {
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }

        // Build Cart Items
        $cartItems = collect();
        if (isset($selectedItems[0]['product_id'])) {
            foreach ($selectedItems as $item) {
                $product = Product::with('user')->findOrFail($item['product_id']);
                $cartItems->push((object)[
                    'product' => $product,
                    'quantity' => (int)$item['quantity'],
                    'id' => 0,
                ]);
            }
        } else {
            $cartItems = Cart::where('user_id', $user->id)
                ->whereIn('id', $selectedItems)
                ->with('product.user')
                ->get();
        }

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid items found.']);
        }

        // Totals
        $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
        $fulfillment = $request->input('fulfillment_method', 'delivery');
        $totalShipping = $fulfillment === 'pickup' ? 0 : 50 * $cartItems->pluck('product.user_id')->unique()->count();
        $grandTotal = $subtotal + $totalShipping;

        // Create Order
        $order = Order::create([
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
                'product_id' => $cartItem->product->id,
                'quantity'   => $cartItem->quantity,
                'price'      => $cartItem->product->price,
            ]);
            if ($cartItem->id != 0) $cartItem->delete();
        }

        // Notify Sellers
        $order->load('orderItems.product.user');
        $itemsBySeller = $order->orderItems->groupBy(fn($i) => $i->product->user_id);
        foreach ($itemsBySeller as $sellerId => $items) {
            if ($seller = User::find($sellerId)) {
                Notification::send($seller, new NewOrderForSeller($order, $items));
            }
        }

        // ✅ Handle Payment
        if ($request->payment_method === 'online') {
            try {
                $config = Configuration::getDefaultConfiguration();
                $config->setApiKey(env('XENDIT_SECRET_KEY'));
                $api = new InvoiceApi(null, $config);

                $params = new CreateInvoiceRequest([
                    'external_id' => 'order-' . $order->id,
                    'payer_email' => $user->email ?? 'customer@example.com',
                    'description' => 'Payment for Order #' . $order->id,
                    'amount' => (float)$grandTotal,
                    'success_redirect_url' => route('checkout.success'),
                    'failure_redirect_url' => route('checkout.show'),
                    'payment_methods' => ['GCASH', 'GRABPAY', 'PAYMAYA', 'QRPH', 'CARD'],
                ]);

                $invoice = $api->createInvoice($params);
                $order->update([
                    'payment_reference' => $invoice->getId(),
                    'invoice_url' => $invoice->getInvoiceUrl(),
                ]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $invoice->getInvoiceUrl(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Xendit Invoice Error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create Xendit invoice.',
                ], 500);
            }
        }

        // ✅ COD Fallback
        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'redirect_url' => route('checkout.success'),
        ]);
    }

    /**
     * Success Page
     */
    public function success()
    {
        $products = Product::latest()->take(6)->get();
        return view('checkout.success', compact('products'));
    }

    /**
     * Save Selected Address
     */
    public function saveSelectedAddress(Request $request)
    {
        $request->validate(['address_id' => 'required|exists:addresses,id']);
        session(['selected_address_id' => $request->address_id]);
        return response()->json(['success' => true]);
    }
}
