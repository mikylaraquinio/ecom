<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Invoice — Order #{{ $order->id }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #fff;
      color: #333;
      font-size: 14px;
    }
    .table th, .table td { vertical-align: middle !important; }
    .fw-bold { font-weight: 600 !important; }
    .invoice-header { border-bottom: 2px solid #28a745; }
    .text-success { color: #198754 !important; }
    .divider { border-top: 1px solid #dee2e6; margin: 1rem 0; }
    .small { font-size: 0.9rem; }
  </style>
</head>

<body class="p-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-start invoice-header pb-2 mb-3">
    <div>
      <h5 class="fw-bold text-success mb-0">FarmSmart Marketplace</h5>
      <small class="text-muted">Transaction E-Invoice</small><br>
      <small>Issued: {{ $order->updated_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</small>
    </div>
    <div class="text-end">
      <h6 class="fw-bold">Invoice No: INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h6>
      <small>Order ID: {{ $order->id }}</small>
    </div>
  </div>

  @php
    $buyer = $order->user;
    $seller = optional($order->orderItems->first()?->product?->user);
    $sellerAddress = optional($seller->seller)?->pickup_address
        ?? ($seller->city . ', ' . $seller->province ?? '');
    $buyerAddress = optional($order->address);
  @endphp

  <!-- Seller & Buyer Information -->
  <div class="row mb-3">
    <div class="col-md-6">
      <h6 class="text-success fw-bold mb-2">
        <i class="fas fa-store me-2"></i> Seller Information
      </h6>
      <p class="mb-0"><strong>{{ $seller->name ?? 'Unknown Seller' }}</strong></p>
      <small>{{ $sellerAddress ?: 'No address available' }}</small><br>
      <small>Contact: {{ $seller->phone ?? '—' }}</small>
    </div>
    <div class="col-md-6">
      <h6 class="text-success fw-bold mb-2">
        <i class="fas fa-user me-2"></i> Buyer Information
      </h6>
      <p class="mb-0"><strong>{{ $buyer->name }}</strong></p>
      <small>
        @if($buyerAddress)
          {{ $buyerAddress->floor_unit_number ? $buyerAddress->floor_unit_number . ', ' : '' }}
          {{ $buyerAddress->barangay ? $buyerAddress->barangay . ', ' : '' }}
          {{ $buyerAddress->city ? $buyerAddress->city . ', ' : '' }}
          {{ $buyerAddress->province }}
        @else
          No address provided
        @endif
      </small><br>
      <small>Contact: {{ $buyerAddress->mobile_number ?? $buyer->phone ?? '—' }}</small>
    </div>
  </div>

  <div class="divider"></div>

  <!-- Order Details -->
  <h6 class="text-success fw-bold mb-2">
    <i class="fas fa-box me-2"></i> Order Details
  </h6>
  <table class="table table-sm table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Product</th>
        <th class="text-center">Variation</th>
        <th class="text-center">Qty</th>
        <th class="text-end">Unit Price</th>
        <th class="text-end">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->orderItems as $item)
        <tr>
          <td>{{ $item->product->name }}</td>
          <td class="text-center">{{ $item->product->variation ?? '—' }}</td>
          <td class="text-center">{{ $item->quantity }}</td>
          <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
          <td class="text-end">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  @php
    $subtotal = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
    $shipping = $order->shipping_fee ?? 0;
  @endphp

  <!-- Summary -->
  <div class="text-end mt-2">
    <p class="mb-1">Subtotal: <strong>₱{{ number_format($subtotal, 2) }}</strong></p>
    <p class="mb-1">Shipping Subtotal: <strong>₱{{ number_format($shipping, 2) }}</strong></p>
    <p class="mb-1">Shipping Discount Subtotal: <strong>- ₱0.00</strong></p>
    <h5 class="text-success fw-bold mt-3">
      Grand Total: ₱{{ number_format($order->total_amount, 2) }}
    </h5>
  </div>

  <div class="divider"></div>

  <!-- Footer Info -->
  <div class="d-flex justify-content-between align-items-start small text-muted">
    <div>
      <p class="mb-1">Payment Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
      <p class="mb-1">Order Placed: {{ $order->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
      <p class="mb-0">Order Paid Date:
        {{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}
      </p>
    </div>
    <div class="text-end">
      <small>Thank you for shopping at <strong>FarmSmart</strong>!</small><br>
      <small>This serves as your official e-invoice for Cash on Delivery payment.</small>
    </div>
  </div>

  <!-- Print Button (for browser preview) -->
  <div class="text-end mt-4">
    <button onclick="window.print()" class="btn btn-outline-success">
      <i class="fas fa-print me-1"></i> Print / Save PDF
    </button>
  </div>
</body>
</html>
