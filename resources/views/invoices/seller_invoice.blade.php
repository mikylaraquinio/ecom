<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller E-Invoice - Order #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 30px; color: #222; }
        .header, .footer { text-align: center; }
        .header h2 { color: #198754; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 13px; }
        th { background-color: #f8f9fa; text-align: left; }
        .summary td { border: none; padding: 5px; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-success { color: #198754; }
        .small { font-size: 12px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h2>FarmSmart Marketplace</h2>
        <p>Seller E-Invoice (Internal Copy)</p>
        <p class="small">Issued: {{ now()->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
    </div>

    <hr>

    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div>
            <h4 class="text-success">Seller Information</h4>
            @php
                $seller = optional($order->orderItems->first()?->product?->user);
                $sellerAddress = optional($seller->seller)?->pickup_address ?? ($seller->city . ', ' . $seller->province ?? '');
            @endphp
            <p><strong>{{ $seller->name ?? 'Unknown Seller' }}</strong><br>
            {{ $sellerAddress ?: 'No address available' }}<br>
            Contact: {{ $seller->phone ?? '—' }}</p>
        </div>

        <div>
            <h4 class="text-success">Buyer Information</h4>
            @php
                $buyer = $order->user;
                $buyerAddress = optional($order->address);
            @endphp
            <p><strong>{{ $buyer->name }}</strong><br>
            @if($buyerAddress)
                {{ $buyerAddress->floor_unit_number ? $buyerAddress->floor_unit_number . ', ' : '' }}
                {{ $buyerAddress->barangay ? $buyerAddress->barangay . ', ' : '' }}
                {{ $buyerAddress->city ? $buyerAddress->city . ', ' : '' }}
                {{ $buyerAddress->province }}
            @else
                No address provided
            @endif<br>
            Contact: {{ $buyerAddress->mobile_number ?? $buyer->phone ?? '—' }}</p>
        </div>
    </div>

    <hr>

    <h4 class="text-success">Order Details</h4>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                    <td class="text-end">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        @php
            $subtotal = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
            $shipping = $order->shipping_fee ?? 0;
        @endphp
        <tr><td class="text-end fw-bold">Subtotal:</td><td class="text-end">₱{{ number_format($subtotal, 2) }}</td></tr>
        <tr><td class="text-end fw-bold">Shipping Fee:</td><td class="text-end">₱{{ number_format($shipping, 2) }}</td></tr>
        <tr><td class="text-end fw-bold">Grand Total:</td><td class="text-end text-success fw-bold">₱{{ number_format($order->total_amount, 2) }}</td></tr>
    </table>

    <hr>

    <div class="footer">
        <p class="small">
            Payment Method: <strong>{{ strtoupper($order->payment_method) }}</strong><br>
            Order ID: #{{ $order->id }}<br>
            Generated automatically for seller records.
        </p>
        <p><strong>FarmSmart Marketplace</strong></p>
    </div>

</body>
</html>
