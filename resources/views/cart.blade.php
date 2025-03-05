<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4"> Shopping Cart</h2>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach(session('cart') as $id => $item)
                                @php $subtotal = $item['price'] * $item['quantity']; @endphp
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="50">
                                        {{ $item['name'] }}
                                    </td>
                                    <td>${{ number_format($item['price'], 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control w-50 d-inline">
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    </td>
                                    <td>${{ number_format($subtotal, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @php $total += $subtotal; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary & Checkout Button -->
                <div class="col-md-4">
                    <div class="card p-3">
                        <h4>Total: ${{ number_format($total, 2) }}</h4>
                        <button class="btn btn-success btn-block" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Checkout Modal -->
            <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="checkoutModalLabel">Checkout</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('checkout.process') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="address" name="address" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="payment" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment" name="payment_method" required>
                                        <option value="credit_card">Credit Card</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="cod">Cash on Delivery</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Confirm & Place Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <p class="text-muted">Your cart is empty. <a href="{{ route('shop') }}">Continue Shopping</a></p>
        @endif
    </div>
</x-app-layout>
