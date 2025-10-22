<x-app-layout>
    <div class="container py-4">

        <!-- ===================== PROFILE HEADER ===================== -->
        <div class="profile-header bg-white border rounded shadow-sm p-4 mb-4 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <label for="profilePictureInput" class="position-relative mb-0">
                    <img id="profilePicturePreview"
                        src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('assets/default.png') }}"
                        alt="Profile Picture"
                        class="rounded-circle border"
                        width="80" height="80"
                        style="object-fit:cover;cursor:pointer;">
                    <span class="position-absolute bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow"
                        style="bottom:0;right:0;width:26px;height:26px;font-size:13px;">
                        <i class="fas fa-pen"></i>
                    </span>
                    <input type="file" id="profilePictureInput" class="d-none" accept="image/*" onchange="uploadProfilePicture(event)">
                </label>

                <div class="ml-3" style="margin-left: 15px;">
                    @php
                        $user = auth()->user();
                        $username = $user->username ?: '@user' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                        $ordersCount = $user->orders()->count();
                        $wishlistCount = $user->wishlist()->count();
                    @endphp
                    <h5 class="font-weight-bold mb-1">{{ $username }}</h5>
                    <small class="text-muted">{{ $user->email }}</small>
                </div>
            </div>

            <div>
                @if($user->role !== 'seller')
                    <a class="btn btn-outline-success" 
                        data-bs-toggle="modal" 
                        data-bs-target="#ModalCreate">
                            <i class="fas fa-store mr-1"></i> Start Selling
                    </a>
                @else
                    <a href="{{ route('myshop') }}" class="btn btn-outline-dark">
                        <i class="fas fa-store mr-1"></i> My Shop
                    </a>
                @endif
            </div>
        </div>

        <!-- ===================== TWO-COLUMN LAYOUT ===================== -->
        <div class="row">
            <!-- LEFT COLUMN: ORDERS -->
            <div class="col-md-7 mb-4">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="card-header bg-white border-0 border-bottom d-flex justify-content-between align-items-center py-3 px-4">
                    <h5 class="fw-bold mb-0 text-success">
                        <i class="fas fa-box-open me-2"></i>Your Orders
                        <span class="badge bg-success text-white">{{ $ordersCount }}</span>
                    </h5>
                    <select id="orderFilter" class="form-select form-select-sm w-auto">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="canceled">Canceled</option>
                    </select>
                    </div>

                    <!-- Orders List -->
                    <div class="card-body bg-light" id="orderList">
                    @forelse($user->orders()->with('orderItems.product')->latest()->get() as $order)
                        @foreach($order->orderItems as $item)
                            @php
                                $product = $item->product;
                                $imageUrl = $product && $product->image ? asset('storage/' . $product->image) : asset('assets/products.jpg');
                                $shippingFee = $order->shipping_fee ?? 0;
                                $total = ($product->price * $item->quantity) + $shippingFee;
                            @endphp

                            <div class="order-card shadow-sm bg-white rounded-3 p-3 mb-3 border position-relative"
                                data-status="{{ strtolower($order->status) }}"
                                data-bs-toggle="modal"
                                data-bs-target="#orderModal-{{ $order->id }}"
                                style="cursor:pointer; transition: all 0.2s ease;">
                                <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $imageUrl }}" width="70" height="70" class="rounded me-3 border" style="object-fit:cover;">
                                    <div>
                                    <h6 class="fw-semibold text-dark mb-1">{{ $product->name }}</h6>
                                    <div class="text-muted small">
                                        ₱{{ number_format($product->price,2) }} × {{ $item->quantity }}  
                                        <span class="mx-1">•</span>  
                                        Shipping ₱{{ number_format($shippingFee,2) }}
                                    </div>
                                    <div class="fw-semibold mt-1 text-danger">₱{{ number_format($total,2) }}</div>
                                    </div>
                                </div>
                                <span class="order-status-badge {{ strtolower($order->status) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                </div>
                            </div>

                        <div class="modal fade" id="orderModal-{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-receipt me-2"></i> Order #{{ $order->id }}
                                            @if($order->status === 'canceled')
                                                <span class="badge bg-danger ms-2"><i class="fas fa-ban me-1"></i> Canceled</span>
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        @php
                                            $isPickup = $order->fulfillment_method === 'pickup';
                                            $address = optional($order->address);
                                        @endphp

                                        <!-- ========================= IF PICKUP ========================= -->
                                        @if($isPickup)
                                            <h6 class="text-success font-weight-bold mb-2">
                                                <i class="fas fa-store mr-2"></i>Pickup Information
                                            </h6>

                                            @php
                                                // Try to fetch the seller info from the first item
                                                $firstItem = $order->orderItems->first();
                                                $seller = optional($firstItem?->product?->user?->seller);
                                            @endphp

                                            @if($seller)
                                                <p><strong>Pickup Location:</strong><br>
                                                    {{ $seller->pickup_address ?? 'No pickup address available' }}
                                                </p>
                                                <p><strong>Pickup Contact:</strong><br>
                                                    {{ $seller->pickup_phone ?? '—' }}
                                                </p>
                                            @else
                                                <p class="text-muted">Pickup details will be provided by the seller.</p>
                                            @endif

                                            <hr>

                                            <h6 class="text-success font-weight-bold mb-3">
                                                <i class="fas fa-truck-loading mr-2"></i>Pickup Progress
                                            </h6>

                                            <div class="order-tracker">
                                                <div class="step {{ $order->created_at ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                                                    <div class="text">
                                                        <strong>Placed</strong>
                                                        <small>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                                <div class="step {{ in_array($order->status, ['accepted','ready_for_pickup','completed']) ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-box"></i></div>
                                                    <div class="text">
                                                        <strong>Accepted</strong>
                                                        <small>{{ $order->accepted_at ? \Carbon\Carbon::parse($order->accepted_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                                <div class="step {{ in_array($order->status, ['ready_for_pickup','completed']) ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-store"></i></div>
                                                    <div class="text">
                                                        <strong>Ready for Pickup</strong>
                                                        <small>{{ $order->ready_at ? \Carbon\Carbon::parse($order->ready_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                                <div class="step {{ $order->status === 'completed' ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-box-open"></i></div>
                                                    <div class="text">
                                                        <strong>Picked Up</strong>
                                                        <small>{{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- ========================= DELIVERY MODE ========================= -->
                                            <h6 class="text-success font-weight-bold mb-2">
                                                <i class="fas fa-user mr-2"></i>Delivery Information
                                            </h6>

                                            <p><strong>Name:</strong> {{ $address->full_name ?? $user->name ?? '—' }}</p>
                                            <p><strong>Contact:</strong> {{ $address->mobile_number ?? $user->phone ?? '—' }}</p>
                                            <p><strong>Address:</strong>
                                                @if($address && ($address->province || $address->city || $address->barangay))
                                                    {{ $address->floor_unit_number ? $address->floor_unit_number . ', ' : '' }}
                                                    {{ $address->barangay ? $address->barangay . ', ' : '' }}
                                                    {{ $address->city ? $address->city . ', ' : '' }}
                                                    {{ $address->province }}
                                                @else
                                                    No address provided
                                                @endif
                                            </p>

                                            <hr>

                                            <h6 class="text-success font-weight-bold mb-3">
                                                <i class="fas fa-truck mr-2"></i>Shipping Progress
                                            </h6>

                                            <div class="order-tracker">
                                                <div class="step {{ $order->created_at ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                                                    <div class="text"><strong>Placed</strong>
                                                        <small>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                                <div class="step {{ in_array($order->status, ['accepted','shipped','completed']) ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-box"></i></div>
                                                    <div class="text"><strong>Accepted</strong>
                                                        <small>{{ $order->accepted_at ? \Carbon\Carbon::parse($order->accepted_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                                <div class="step {{ in_array($order->status, ['shipped','completed']) ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-truck"></i></div>
                                                    <div class="text"><strong>Shipped</strong>
                                                        <small>{{ $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A')  : '—' }}</small>
                                                    </div>
                                                </div>
                                                <div class="step {{ $order->status === 'completed' ? 'active' : '' }}">
                                                    <div class="icon"><i class="fas fa-box-open"></i></div>
                                                    <div class="text"><strong>Delivered</strong>
                                                        <small>{{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <hr>

                                        <!-- Common Section (for both pickup & delivery) -->
                                        <h6 class="text-success font-weight-bold mb-2"><i class="fas fa-box-open mr-2"></i>Ordered Items</h6>
                                        @foreach($order->orderItems as $sub)
                                            <div class="d-flex justify-content-between border-bottom py-1">
                                                <span>{{ $sub->product->name }} × {{ $sub->quantity }}</span>
                                                <span>₱{{ number_format($sub->price * $sub->quantity, 2) }}</span>
                                            </div>
                                        @endforeach
                                        <hr>

                                        <h6 class="text-success font-weight-bold mb-2"><i class="fas fa-file-invoice mr-2"></i>Payment & Invoice</h6>
                                        <p><strong>Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                                        <p><strong>Reference:</strong> {{ $order->payment_reference ?? '—' }}</p>
                                        <p><strong>Total:</strong> ₱{{ number_format($order->total_amount ?? $order->total_price, 2) }}</p>
                                        <p><strong>Shipping Fee:</strong> ₱{{ number_format($order->shipping_fee, 2) }}</p>

                                        @if($order->payment_method === 'online' && $order->invoice_url)
                                            {{-- ✅ Buyer sees Xendit invoice --}}
                                            <a href="{{ $order->invoice_url }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                                                <i class="fas fa-file-invoice me-1"></i> View Xendit Invoice
                                            </a>
                                        @elseif($order->payment_method === 'cod' && $order->invoice_generated)
                                            {{-- ✅ Buyer sees COD PDF only when generated --}}
                                            <a href="{{ $order->invoice_url }}" target="_blank" class="btn btn-outline-success btn-sm mt-2">
                                                <i class="fas fa-file-invoice me-1"></i> View E-Invoice
                                            </a>
                                        @else
                                            <span class="text-muted small">No invoice available yet.</span>
                                        @endif
                                    </div>                              
                                  
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                        @php
                                            $canCancel = $order->status === 'pending' &&
                                                \Carbon\Carbon::parse($order->created_at)->diffInHours(now()) < 24;
                                        @endphp

                                        @if($canCancel)
                                            <button class="btn btn-danger cancel-order-btn" data-id="{{ $order->id }}">
                                                <i class="fas fa-times-circle me-1"></i> Cancel Order
                                            </button>
                                        @elseif($order->status === 'pending')
                                            <button class="btn btn-outline-secondary" disabled>
                                                <i class="fas fa-lock me-1"></i> Cancellation Locked (after 24h)
                                            </button>
                                        @endif

                                        @if($order->payment_method === 'cod' && $order->status === 'completed')
                                            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#invoiceModal-{{ $order->id }}">
                                                <i class="fas fa-file-invoice me-1"></i> View E-Invoice
                                            </button>
                                        @endif

                                        {{-- ✅ Leave Review button (only for completed orders) --}}
                                        @if($order->status === 'completed')
                                            <button type="button" 
                                                    class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#reviewModal-{{ $item->id }}">
                                                <i class="fas fa-star me-1"></i> Leave Review
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($order->payment_method === 'cod' && $order->status === 'completed')
                            <div class="modal fade" id="invoiceModal-{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content shadow-lg border-0">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">
                                    <i class="fas fa-file-invoice me-2"></i> E-Invoice — Order #{{ $order->id }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body px-4 py-3">
                                    @php
                                        $buyer = $order->user;
                                        $seller = optional($order->orderItems->first()?->product?->user);
                                        $sellerAddress = optional($seller->seller)?->pickup_address 
                                            ?? ($seller->city . ', ' . $seller->province ?? '');
                                        $buyerAddress = optional($order->address);
                                    @endphp

                                    <!-- Header Info -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-success">FarmSmart Marketplace</h6>
                                        <small>Transaction E-Invoice</small><br>
                                        <small class="text-muted">Issued: {{ $order->updated_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="fw-bold">Invoice No: INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h6>
                                        <small>Order ID: {{ $order->id }}</small>
                                    </div>
                                    </div>

                                    <hr>

                                    <!-- Seller & Buyer Info -->
                                    <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-success fw-bold"><i class="fas fa-store me-2"></i>Seller Information</h6>
                                        <p class="mb-0"><strong>{{ $seller->name ?? 'Unknown Seller' }}</strong></p>
                                        <small>{{ $sellerAddress ?: 'No address available' }}</small><br>
                                        <small>Contact: {{ $seller->phone ?? '—' }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success fw-bold"><i class="fas fa-user me-2"></i>Buyer Information</h6>
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

                                    <hr>

                                    <!-- Order Details Table -->
                                    <h6 class="text-success fw-bold mb-2"><i class="fas fa-box me-2"></i>Order Details</h6>
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
                                        @foreach($order->orderItems as $sub)
                                        <tr>
                                            <td>{{ $sub->product->name }}</td>
                                            <td class="text-center">{{ $sub->product->variation ?? '—' }}</td>
                                            <td class="text-center">{{ $sub->quantity }}</td>
                                            <td class="text-end">₱{{ number_format($sub->price, 2) }}</td>
                                            <td class="text-end">₱{{ number_format($sub->price * $sub->quantity, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    </table>

                                    <hr>

                                    <!-- Summary -->
                                    <div class="text-end">
                                    @php
                                        $subtotal = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
                                        $shipping = $order->shipping_fee ?? 0;
                                    @endphp

                                    <p class="mb-1">Subtotal: <strong>₱{{ number_format($subtotal, 2) }}</strong></p>
                                    <p class="mb-1">Shipping Subtotal: <strong>₱{{ number_format($shipping, 2) }}</strong></p>
                                    <p class="mb-1">Shipping Discount Subtotal: <strong>- ₱0.00</strong></p>
                                    <h5 class="text-success mt-3">Grand Total: ₱{{ number_format($order->total_amount, 2) }}</h5>
                                    </div>

                                    <hr>

                                    <!-- Footer Info -->
                                    <div class="d-flex justify-content-between mt-3 small text-muted">
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
                                </div>

                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-outline-success" onclick="printInvoice({{ $order->id }})">
                                    <i class="fas fa-print me-1"></i> Print / Save PDF
                                    </button>
                                </div>
                                </div>
                            </div>
                            </div>
                            @endif

                        @endforeach
                    @empty
                        <p class="text-center text-muted py-5 mb-0">No orders found yet.</p>
                    @endforelse
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-md-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-around align-items-center">
                        <button class="btn btn-sm btn-link font-weight-bold active" id="tabWishlist">
                            Wishlist <span class="badge badge-success">{{ $wishlistCount }}</span>
                        </button>
                        <button class="btn btn-sm btn-link font-weight-bold" id="tabSettings">Settings</button>
                    </div>

                    <!-- Wishlist -->
                    <div class="card-body" id="wishlistView">
                        @forelse($wishlistItems as $product)
                            <div class="d-flex align-items-center border-bottom py-2">
                                <img src="{{ asset('storage/'.$product->image) }}" width="60" height="60" class="rounded mr-3" style="object-fit:cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                    <small class="text-success">₱{{ number_format($product->price,2) }}</small>
                                </div>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form mr-2" data-id="{{ $product->id }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-cart-plus"></i></button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger toggle-wishlist-btn" data-id="{{ $product->id }}"><i class="fas fa-trash"></i></button>
                            </div>
                        @empty
                            <p class="text-muted">No items in wishlist.</p>
                        @endforelse
                    </div>

                    <!-- Settings -->
                    <div class="card-body d-none" id="settingsView">
                        <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" class="form-control" name="username"
                                                value="{{ old('username', auth()->user()->username) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone"
                                                value="{{ old('phone', auth()->user()->phone) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Birthdate</label>
                                            <input type="date" class="form-control" name="birthdate"
                                                value="{{ old('birthdate', auth()->user()->birthdate) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ old('name', auth()->user()->name) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="male" {{ auth()->user()->gender === 'male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="female" {{ auth()->user()->gender === 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email', auth()->user()->email) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Place all review modals outside containers --}}
@foreach($user->orders()->with('orderItems.product')->latest()->get() as $order)
    @if($order->status === 'completed')
        @include('partials.review-modal', ['order' => $order])
    @endif
@endforeach


{{-- ✅ Ensure modal triggers always work --}}
<script>
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(el => {
  el.addEventListener('click', e => {
    const target = document.querySelector(el.getAttribute('data-bs-target'));
    if (target) new bootstrap.Modal(target).show();
  });
});
</script>


    <style>
    .order-card:hover{background:#f9f9f9;transition:0.2s;box-shadow:0 2px 6px rgba(0,0,0,0.05);}
    .card-header button.active{color:#28a745;text-decoration:underline;}
    .order-tracker{position:relative;display:flex;justify-content:space-between;margin:25px 0;text-align:center;}
    .order-tracker::before{content:'';position:absolute;top:18px;left:50%;transform:translateX(-50%);width:90%;height:3px;background:#dee2e6;z-index:0;}
    .order-tracker .step{position:relative;z-index:1;flex:1;}
    .order-tracker .icon{background:#dee2e6;color:#6c757d;width:36px;height:36px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-size:16px;}
    .order-tracker .step.active .icon{background:#28a745;color:#fff;box-shadow:0 0 8px rgba(40,167,69,0.4);}
    .order-tracker .text strong{display:block;font-size:0.9rem;}
    .order-tracker .text small{color:#6c757d;font-size:0.8rem;}
    .profile-topcard {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 12px;
        padding: 16px 24px;
        background: #fff;
        transition: box-shadow 0.2s ease-in-out;
    }

    .profile-topcard:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    /* Profile Image */
    .profile-topcard img {
        border: 2px solid #e5e7eb;
        width: 70px;
        height: 70px;
        object-fit: cover;
    }

    /* Edit Icon */
    .profile-topcard .position-absolute {
        bottom: 0;
        right: 0;
        width: 22px;
        height: 22px;
        font-size: 11px;
    }

    /* Username & Email */
    .profile-topcard .ml-3 h5 {
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px; /* Adds spacing between username and email */
        font-size: 1.1rem;
    }

    .profile-topcard .ml-3 small {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .order-status-badge.canceled {
        background-color: #dc3545 !important;
        color: #fff;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.85rem;
    }
    .modal-backdrop.show {
        opacity: 0.45 !important;
    }


    </style>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Toggle wishlist/settings
    $("#tabWishlist").click(function(){
        $(this).addClass("active");
        $("#tabSettings").removeClass("active");
        $("#wishlistView").removeClass("d-none");
        $("#settingsView").addClass("d-none");
    });
    $("#tabSettings").click(function(){
        $(this).addClass("active");
        $("#tabWishlist").removeClass("active");
        $("#settingsView").removeClass("d-none");
        $("#wishlistView").addClass("d-none");
    });

    // Filter orders
    $("#orderFilter").on("change",function(){
        const val=$(this).val().toLowerCase();
        $(".order-card").each(function(){
            const status=$(this).data("status");
            $(this).toggle(val==="all"||status===val);
        });
    });

    // Add to Cart
    $(".add-to-cart-form").on("submit",function(e){
        e.preventDefault();
        const id=$(this).data("id");
        fetch(`/cart/add/${id}`,{
            method:"POST",
            headers:{
                "X-CSRF-TOKEN":"{{ csrf_token() }}",
                "Content-Type":"application/json"
            },
            body:JSON.stringify({quantity:1})
        }).then(r=>r.json()).then(data=>{
            Swal.fire("Added!","Product added to cart.","success");
        });
    });

    // Remove from wishlist
    $(".toggle-wishlist-btn").click(function(){
        const id=$(this).data("id");
        fetch(`/wishlist/toggle/${id}`,{
            method:"POST",
            headers:{
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            }
        }).then(r=>r.json()).then(data=>{
            Swal.fire("Removed!","Item removed from wishlist.","info");
            $(this).closest(".d-flex").remove();
        });
    });

    // Upload Profile Picture
    function uploadProfilePicture(event){
        const file=event.target.files[0];
        if(!file)return;
        const formData=new FormData();
        formData.append("profile_picture",file);
        fetch('{{ route('profile.updatePicture') }}',{
            method:"POST",
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body:formData
        }).then(r=>r.json()).then(data=>{
            if(data.success){
                $("#profilePicturePreview").attr("src",data.path);
                Swal.fire("Updated!","Profile picture updated.","success");
            }else Swal.fire("Error","Upload failed.","error");
        });
    }
    // 🛑 Cancel Order (AJAX)
    $(document).on("click", ".cancel-order-btn", function() {
        const orderId = $(this).data("id");

        Swal.fire({
            title: "Cancel this order?",
            text: "You can only cancel within 24 hours after placing it.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, cancel it"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/orders/${orderId}/cancel`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Canceled!", data.message, "success");
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Something went wrong.", "error"));
            }
        });
    });
    
    function printInvoice(orderId) {
        const modalBody = document.querySelector(`#invoiceModal-${orderId} .modal-body`).innerHTML;
        const w = window.open('', '_blank', 'width=900,height=1000');
        w.document.write(`
            <html>
                <head>
                    <title>Invoice #${orderId}</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
                </head>
                <body class="p-4">
                    ${modalBody}
                    <script>window.print();<\/script>
                </body>
            </html>
        `);
        w.document.close();
    }
    </script>
    <script>
document.addEventListener('hidden.bs.modal', function (event) {
  // Remove all leftover backdrops when any modal closes
  const backdrops = document.querySelectorAll('.modal-backdrop');
  backdrops.forEach(b => b.remove());
  document.body.classList.remove('modal-open');
  document.body.style.overflow = ''; // allow scrolling again
});
</script>


    @include('farmers.modal.sell')
</x-app-layout>
