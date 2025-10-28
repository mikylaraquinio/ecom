<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Seller Analytics Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    h2 { color: #28a745; margin-bottom: 0; }
    small { color: #666; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
    th { background: #f8f8f8; }
    .summary { margin-top: 15px; }
    .summary td { border: none; padding: 4px 8px; }
    .text-right { text-align: right; }
  </style>
</head>
<body>
  <h2>Analytics Report</h2>
  <small>
    Seller: <strong>{{ $seller->seller->shop_name ?? $seller->name }}</strong><br>
    Period: 
    @if($type === 'Custom')
      {{ $start }} - {{ $end }}
    @else
      {{ $type }}
    @endif
  </small>

  <table class="summary">
    <tr><td><strong>Total Revenue:</strong></td><td>₱{{ number_format($completedSales, 2) }}</td></tr>
    <tr><td><strong>Total Orders:</strong></td><td>{{ $totalOrders }}</td></tr>
    <tr><td><strong>Average Order Value:</strong></td><td>₱{{ number_format($avgOrderValue, 2) }}</td></tr>
    <tr><td><strong>Unique Customers:</strong></td><td>{{ $uniqueCustomers }}</td></tr>
  </table>

  <h4>Order Status Breakdown</h4>
  <table>
    <thead>
      <tr>
        <th>Pending</th>
        <th>Accepted</th>
        <th>Shipped</th>
        <th>Completed</th>
        <th>Canceled</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $pendingOrders }}</td>
        <td>{{ $acceptedOrders }}</td>
        <td>{{ $shippedOrders }}</td>
        <td>{{ $completedOrders }}</td>
        <td>{{ $canceledOrders }}</td>
      </tr>
    </tbody>
  </table>

  <h4>Revenue Trends</h4>
  <table>
    <thead><tr><th>Date</th><th class="text-right">Revenue (₱)</th></tr></thead>
    <tbody>
      @forelse($salesTrends as $date => $total)
        <tr><td>{{ $date }}</td><td class="text-right">{{ number_format($total, 2) }}</td></tr>
      @empty
        <tr><td colspan="2" class="text-center">No sales during this period.</td></tr>
      @endforelse
    </tbody>
  </table>

  <h4>Recent Orders</h4>
    <table>
    <thead>
        <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Date</th>
        <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($recentOrders as $order)
        <tr>
            <td>#{{ $order->id }}</td>
            <td>{{ $order->user->name ?? 'N/A' }}</td>
            <td>₱{{ number_format($order->total_amount, 2) }}</td>
            <td>{{ $order->created_at->format('M d, Y') }}</td>
            <td>
            {{ ucfirst($order->status) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No orders during this period.</td>
        </tr>
        @endforelse
    </tbody>
    </table>
</body>
</html>
