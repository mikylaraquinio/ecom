<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (kept in DOM but hidden by CSS) -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <button class="btn btn-light d-lg-none mb-2" 
                        data-toggle="collapse" 
                        data-target="#sidebarNav" 
                        aria-expanded="false" 
                        aria-controls="sidebarNav">
                    Menu
                </button>
                <div class="position-sticky">
                    <div class="text-center py-4 border-bottom">
                        <!-- Profile Picture with Edit Icon -->
                        <label for="profilePictureInput" class="position-relative d-inline-block">
                            <img src="{{ auth()->user()->profile_picture
                                ? asset('storage/' . auth()->user()->profile_picture)
                                : asset('assets/default.png') }}" alt="Profile Picture" class="rounded-circle mb-2"
                                width="80" height="80"
                                style="border: 3px solid #fff; object-fit: cover; aspect-ratio: 1/1;">

                            <!-- Edit Icon -->
                            <div class="position-absolute d-flex justify-content-center align-items-center"
                                style="width: 30px; height: 30px; bottom: 0; right: 0;">
                                <div class="bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="fas fa-pen text-white" style="font-size: 14px;"></i>
                                </div>
                            </div>

                            <input type="file" id="profilePictureInput" class="d-none" accept="image/*">
                        </label>

                        <!-- User Info -->
                        <h5 class="text-white">{{ auth()->user()->username }}</h5>
                        <p class="text-white mb-0">{{ auth()->user()->email }}</p>
                    </div>

                    <script>
                        document.getElementById('profilePictureInput')?.addEventListener('change', function (event) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    const img = document.getElementById('profilePicturePreview');
                                    if (img) img.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    </script>

                    <!-- FontAwesome for the Pen Icon -->
                    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

                    <!-- Desktop (≥ lg): classic vertical list WITH icons -->
                    <ul class="nav flex-column mt-3 d-none d-lg-block sidebar-list">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="#user-dashboard" data-toggle="pill">
                                <i class="fas fa-seedling mr-2"></i> Your Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#wishlist-section" data-toggle="pill">
                                <i class="fas fa-heart mr-2"></i> Wishlist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-general" data-toggle="pill">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                        </li>

                        @if(auth()->user()->role !== 'seller')
                            <li class="nav-item">
                                <a href="#" class="nav-link text-white" data-toggle="modal" data-target="#ModalCreate">
                                    <i class="fas fa-tractor mr-2"></i> Start Selling
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('myshop') }}" class="btn text-white shadow-sm rounded-pill px-4 d-flex align-items-center justify-content-center" style="background-color: #8B5E3C;">
                                    <i class="fas fa-store mr-2"></i> My Shop
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 profile-main">

                <!-- Top profile header -->
                <div class="profile-topcard">
                  <div class="ptc-left">
                    <label for="profilePictureInput" class="ptc-avatar">
                      <img id="profilePicturePreview" src="{{ auth()->user()->profile_picture
                          ? asset('storage/' . auth()->user()->profile_picture)
                          : asset('assets/default.png') }}" alt="Profile Picture">
                    </label>
                    <div class="ptc-meta">
                      <div class="ptc-username">{{ auth()->user()->username }}</div>
                      <div class="ptc-email">{{ auth()->user()->email }}</div>
                    </div>
                  </div>
                  <div class="ptc-right">
                    @if(auth()->user()->role !== 'seller')
                      <a href="#" class="ptc-shop" data-toggle="modal" data-target="#ModalCreate">
                        <i class="fas fa-store mr-1"></i> My Shop
                      </a>
                    @else
                      <a href="{{ route('myshop') }}" class="ptc-shop">
                        <i class="fas fa-store mr-1"></i> My Shop
                      </a>
                    @endif
                  </div>
                </div>

                <!-- Top-level tiles (Your Orders / Wishlist / Settings) -->
                <ul class="nav nav-justified profile-toptabs" id="profileTopTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="top-orders" data-toggle="pill"
                      href="#user-dashboard" role="tab" aria-controls="user-dashboard" aria-selected="true">
                      <i class="fas fa-seedling mr-2"></i> Your Orders
                    </a>
                  </li>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link" id="top-wishlist" data-toggle="pill"
                      href="#wishlist-section" role="tab" aria-controls="wishlist-section" aria-selected="false">
                      <i class="fas fa-heart mr-2"></i> Wishlist
                    </a>
                  </li>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link" id="top-settings" data-toggle="pill"
                      href="#account-general" role="tab" aria-controls="account-general" aria-selected="false">
                      <i class="fas fa-cog mr-2"></i> Settings
                    </a>
                  </li>
                </ul>

                <!-- TOP-LEVEL PANES -->
                <div class="tab-content">
                    <!-- Your Orders -->
                    <div class="tab-pane fade show active" id="user-dashboard">
                        <!-- Orders sub-tabs (only show on Your Orders) -->
                        <div id="orders-subtabs">
                            <h5>Your Orders</h5>
                            <ul class="nav nav-tabs" id="orderTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="pill" href="#to-ship">To Ship</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#to-receive">To Receive</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#to-review">To Review</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content mt-2">
                            <!-- To Ship -->
                            <div class="tab-pane fade show active" id="to-ship">
                            @if($ordersToShip->isEmpty())
                                <p>No orders to ship.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($ordersToShip as $order)
                                        @foreach($order->orderItems as $orderItem)
                                            @php
                                                $product = $orderItem->product;
                                                $imageUrl = $product && $product->image
                                                    ? asset('storage/' . $product->image)
                                                    : asset('assets/products.jpg');
                                                $quantity = $orderItem->quantity;
                                                $total = $product->price * $quantity;
                                            @endphp

                                            @if($product)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <!-- Product Image and Info -->
                                                        <div class="d-flex">
                                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                                                class="me-3 rounded"
                                                                style="width: 80px; height: 80px; object-fit: cover;">

                                                            <div>
                                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                                <p class="mb-0 small text-muted">₱{{ number_format($product->price, 2) }} × {{ $quantity }}</p>
                                                                <p class="mb-0 small fw-bold text-dark">Total: ₱{{ number_format($total, 2) }}</p>

                                                                <!-- Order Dates -->
                                                                <p class="mb-0 small text-muted">
                                                                    <strong>Ordered:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                                                                    <strong>Shipped:</strong> 
                                                                    {{ $order->shipped_at ? $order->shipped_at->format('M d, Y') : '—' }}<br>
                                                                    <strong>Delivered:</strong> 
                                                                    {{ $order->delivered_at ? $order->delivered_at->format('M d, Y') : '—' }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <!-- Order Status -->
                                                        <div class="text-end">
                                                            <span class="badge 
                                                                {{ $order->status === 'pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                                {{ $order->status === 'pending' ? 'Pending' : 'Ready to Ship' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Cancel Button Bottom Right -->
                                                    @if($order->status === 'pending' || $order->status === 'accepted')
                                                        <div class="d-flex justify-content-end mt-2">
                                                            <form action="{{ route('buyer.cancelOrder', $order->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to cancel your order?')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="canceled">
                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    Cancel Order
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </li>
                                            @else
                                                <li class="list-group-item text-danger text-center small">
                                                    Product information not available
                                                </li>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        
                        <!-- To Receive -->
                        <div class="tab-pane fade" id="to-receive">
                            @if($ordersToReceive->isEmpty())
                                <p>No orders to receive.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($ordersToReceive as $order)
                                        @foreach($order->orderItems as $orderItem)
                                            @php
                                                $product = $orderItem->product;
                                                $imageUrl = $product && $product->image
                                                    ? asset('storage/' . $product->image)
                                                    : asset('assets/products.jpg');
                                                $quantity = $orderItem->quantity;
                                                $total = $product->price * $quantity;
                                            @endphp

                                            @if($product)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <!-- Product Image and Info -->
                                                        <div class="d-flex">
                                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                                                class="me-3 rounded"
                                                                style="width: 80px; height: 80px; object-fit: cover;">

                                                            <div>
                                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                                <p class="mb-0 small text-muted">₱{{ number_format($product->price, 2) }} × {{ $quantity }}</p>
                                                                <p class="mb-0 small fw-bold text-dark">Total: ₱{{ number_format($total, 2) }}</p>

                                                                <!-- Order Dates -->
                                                                <p class="mb-0 small text-muted">
                                                                    <strong>Ordered:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                                                                    <strong>Shipped:</strong> {{ $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at)->format('M d, Y') : '—' }}
                                                                    <strong>Delivered:</strong> {{ $order->delivered_at ? $order->delivered_at->format('M d, Y') : '—' }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <!-- Status -->
                                                        <div class="text-end">
                                                            <span class="badge bg-info">Shipped</span>
                                                        </div>
                                                    </div>

                                                    <!-- Mark as Received Button -->
                                                    @if($order->status === 'shipped')
                                                        <div class="d-flex justify-content-end mt-2">
                                                            <form action="{{ route('buyer.confirmReceipt', $order->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Confirm that you have received this order?')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    Mark as Received
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </li>
                                            @else
                                                <li class="list-group-item text-danger text-center small">
                                                    Product information not available
                                                </li>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- To Review -->
                        <div class="tab-pane fade" id="to-review">
                            @if($ordersToReview->isEmpty())
                                <p>No orders to review.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($ordersToReview as $order)
                                        @foreach($order->orderItems as $orderItem)
                                            @php
                                                $product = $orderItem->product;
                                                $seller = $product->seller ?? null;
                                                $imageUrl = $product && $product->image
                                                    ? asset('storage/' . $product->image)
                                                    : asset('assets/products.jpg');
                                                $quantity = $orderItem->quantity;
                                                $total = $product->price * $quantity;
                                                $hasReviewed = $orderItem->review;
                                            @endphp

                                            @if($product)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="d-flex">
                                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                                                 class="me-3 rounded"
                                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                                <p class="mb-0 small text-muted">₱{{ number_format($product->price, 2) }} × {{ $quantity }}</p>
                                                                <p class="mb-0 small fw-bold text-dark">Total: ₱{{ number_format($total, 2) }}</p>
                                                                <p class="mb-0 small text-muted">
                                                                    <strong>Delivered:</strong>
                                                                    {{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y') : '—' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-success">Completed</span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-end mt-2">
                                                        @if($hasReviewed)
                                                            <span class="badge bg-secondary">Reviewed</span>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                                    data-toggle="modal"
                                                                    data-target="#rateModal-{{ $orderItem->id }}">
                                                                Rate
                                                            </button>
                                                        @endif
                                                    </div>
                                                </li>

                                                <!-- Review Modal -->
                                                <div class="modal fade" id="rateModal-{{ $orderItem->id }}" tabindex="-1" aria-labelledby="rateModalLabel-{{ $orderItem->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                                <input type="hidden" name="order_item_id" value="{{ $orderItem->id }}">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="rateModalLabel-{{ $orderItem->id }}">Rate Product</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="mb-2">{{ $product->name }}</p>
                                                                    <div class="mb-3">
                                                                        <label class="form-label d-block mb-2">Product Quality</label>
                                                                        <div class="star-rating d-flex align-items-center gap-3">
                                                                            <div class="stars d-flex align-items-center" data-order-id="{{ $orderItem->id }}">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <input type="radio" name="rating" id="star{{ $orderItem->id }}-{{ $i }}" value="{{ $i }}" />
                                                                                    <label for="star{{ $orderItem->id }}-{{ $i }}" data-value="{{ $i }}">★</label>
                                                                                @endfor
                                                                            </div>
                                                                            <span class="rating-label text-muted small" id="rating-label-{{ $orderItem->id }}">Choose rating</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Write a Review</label>
                                                                        <textarea class="form-control" name="review" rows="3" placeholder="Share your thoughts..." required></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Add Photo (optional)</label>
                                                                        <input type="file" class="form-control" name="photo" accept="image/*">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Add Video (optional)</label>
                                                                        <input type="file" class="form-control" name="video" accept="video/*">
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="show_username" value="1" id="showUsername-{{ $orderItem->id }}">
                                                                        <label class="form-check-label" for="showUsername-{{ $orderItem->id }}">
                                                                            Show username on your review
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <li class="list-group-item text-danger text-center small">
                                                    Product information not available
                                                </li>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        </div> <!-- /#to-review -->
                    </div> <!-- /#user-dashboard -->

                    <!-- Wishlist -->
                    <div class="tab-pane fade" id="wishlist-section">
                        <h5 class="mt-4">Your Wishlist</h5>
                        <div class="row">
                            @forelse ($wishlistItems as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            <p class="card-text">₱{{ number_format($product->price, 2) }}</p>
                                            <!-- Add to Cart -->
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-2 add-to-cart-form" data-id="{{ $product->id }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                </button>
                                            </form>
                                            <!-- Remove from Wishlist -->
                                            <button class="btn btn-outline-danger btn-sm w-100 toggle-wishlist-btn" data-id="{{ $product->id }}">
                                                <i class="fas fa-heart-broken me-1"></i> Remove from Wishlist
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No items in your wishlist.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Settings (General) -->
                    <div class="tab-pane fade" id="account-general">
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

                    <!-- (Optional) Separate change password pane -->
                    <div class="tab-pane fade" id="account-change-password">
                        <div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 380px; border-radius: 12px;">
                            <form method="POST" action="{{ route('profile.updatePassword') }}">
                                @csrf
                                <h5 class="mb-3 text-center fw-bold">Change Password</h5>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Current Password</label>
                                    <input type="password" name="current_password" class="form-control rounded-3" placeholder="Enter current password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">New Password</label>
                                    <input type="password" name="new_password" class="form-control rounded-3" placeholder="Enter new password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="form-control rounded-3" placeholder="Re-enter new password" required>
                                </div>

                                <button type="submit" class="btn btn-success w-100 rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-lock me-2"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>

                </div> <!-- /.tab-content (TOP-LEVEL) -->
            </main>
        </div>
    </div>

    <style>
    /* ---------- Layout & spacing ---------- */
    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }
    header.bg-white.shadow { display: none !important; }
    .container-fluid { padding-top: 0 !important; }
    .profile-main { padding-top: 12px !important; padding-bottom: 24px; }

    /* Hide sidebar & let main go full width */
    .sidebar { display: none !important; }
    .profile-main { flex: 0 0 100% !important; max-width: 100% !important; }

    /* Top profile header */
    .profile-topcard {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 12px 14px;
      margin: 8px 0 12px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .ptc-left { display: flex; align-items: center; gap: 10px; }
    .ptc-avatar img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; }
    .ptc-username { font-weight: 700; color: #111827; line-height: 1.1; }
    .ptc-email { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .ptc-shop { display: inline-flex; align-items: center; gap: 6px; background: #fff; color: #374151; text-decoration: none; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; }
    .ptc-shop:hover { background: #f9fafb; }

    /* Cards / common */
    .card { background-clip: padding-box; box-shadow: 0 1px 4px rgba(24, 28, 33, 0.1); border-radius: 10px; padding: 15px; }
    .btn { cursor: pointer; padding: 10px 15px; border-radius: 5px; font-size: 16px; }

    /* Stars (rating) */
    .stars { display: flex; direction: ltr; }
    .stars label { font-size: 2rem; color: #ccc; cursor: pointer; transition: color 0.2s; margin-right: 5px; }
    .stars input[type="radio"] { display: none; }
    .stars label:hover, .stars label:hover ~ label { color: #ffc107; }

    /* Modals */
    .modal-content { border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    .modal-header { background-color: #f3f4f6; padding: 16px; border-bottom: 1px solid #ddd; }
    .modal-title { font-size: 18px; font-weight: 600; color: #333; }
    .modal-body { padding: 20px; }

    /* Inputs */
    label { font-size: 14px; font-weight: 500; color: #444; }
    input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
    input:focus, select:focus, textarea:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 5px rgba(99,102,241,0.3); }

    /* Left-aligned compact tiles */
    .profile-toptabs{
      --tile: 80px;  /* square size */
      --gap: 15px;   /* spacing between tiles */

      display: flex !important;      /* override .nav/.nav-justified */
      justify-content: flex-start;   /* align LEFT */
      align-items: center;
      gap: var(--gap);

      background: #fff;              /* white strip */
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 6px 8px;
      margin: 6px 0 8px;
    }
    .profile-toptabs .nav-item{ flex: 0 0 auto; }
    .profile-toptabs .nav-link{
      width: var(--tile) !important;
      height: var(--tile) !important;
      padding: 0 !important;
      border-radius: 10px;

      display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;

      background: #fff;
      color: #0f172a !important;
      border: 1px solid #e5e7eb;
      font-weight: 700;
      font-size: 11.5px;
      line-height: 1.1;
      text-decoration: none;
      box-shadow: 0 1px 3px rgba(16,24,40,.06);
      transition: transform .12s, box-shadow .12s, border-color .12s, background .12s;
    }
    .profile-toptabs .nav-link i{
      font-size: 18px;
      color: #f97316;
      margin: 0 !important;
    }
    .profile-toptabs .nav-link:hover{
      transform: translateY(-1px);
      box-shadow: 0 6px 14px rgba(16,24,40,.10);
      background: #fcfcfc;
    }
    .profile-toptabs .nav-link.active{
      border-color: #fdba74;
      box-shadow: inset 0 0 0 2px #fde9d7, 0 4px 10px rgba(16,24,40,.08);
    }

    /* tighter on small phones */
    @media (max-width: 420px){
      .profile-toptabs{ --tile: 50px; --gap: 6px; padding: 6px; }
      .profile-toptabs .nav-link{ font-size: 10.5px; }
      .profile-toptabs .nav-link i{ font-size: 16px; }
    }

    /* Only show the active top-level pane (defensive) */
    .profile-main > .tab-content > .tab-pane { display: none; }
    .profile-main > .tab-content > .tab-pane.show.active { display: block; }

    /* Ensure sticky content doesn't create extra top gap on small screens */
    .position-sticky { top: 0; }
    @media (max-width: 991.98px) { .position-sticky { position: static !important; } }
    </style>

    <!-- jQuery + Bootstrap (place BEFORE the custom controller) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Your existing app scripts that use jQuery/Bootstrap can go here (unchanged) -->
    <script>
        // AJAX seller form
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("sellerRegistrationForm");
            if (form) {
              form.addEventListener("submit", function (event) {
                event.preventDefault();
                let formData = new FormData(this);
                fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json()) 
                .then(data => {
                    if (data.success) {
                        $('#ModalCreate').modal('hide');
                        document.getElementById("shopButtonContainer")?.innerHTML = `
                            <div class="text-center mt-3">
                                <a href="{{ route('myshop') }}" class="btn text-white shadow-sm rounded-pill px-4 d-flex align-items-center justify-content-center" style="background-color: #8B5E3C;">
                                    <i class="fas fa-basket me-2"></i> My Shop
                                </a>
                            </div>
                        `;
                    } else {
                        alert("Registration failed! Please try again.");
                    }
                })
                .catch(error => console.error("Error:", error));
              });
            }
        });

        // Add to cart & wishlist toggle (unchanged)
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const productId = this.dataset.id;
                const quantity = 1;
                fetch(`/cart/add/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong!' });
                    console.error(err);
                });
            });
        });

        document.querySelectorAll('.toggle-wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const productId = this.dataset.id;
                const button = this;
                fetch(`/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'removed') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Removed',
                            text: 'Item removed from wishlist.',
                            timer: 1200,
                            showConfirmButton: false
                        });
                        button.closest('.col-md-4').remove();
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not remove item from wishlist.' });
                    console.error(err);
                });
            });
        });

        // Stars widget
        document.querySelectorAll('.stars').forEach(starGroup => {
            const orderId = starGroup.getAttribute('data-order-id');
            const label = document.getElementById(`rating-label-${orderId}`);
            const stars = starGroup.querySelectorAll('label');
            const radios = starGroup.querySelectorAll('input[type="radio"]');
            const labels = ["Terrible", "Poor", "Average", "Good", "Amazing"];

            stars.forEach((star) => {
                const value = parseInt(star.getAttribute('data-value'));
                star.addEventListener('mouseenter', () => {
                    stars.forEach((s, i) => { s.style.color = (i < value) ? '#ffc107' : '#ccc'; });
                    if (label) label.textContent = labels[value - 1];
                });
                star.addEventListener('mouseleave', () => {
                    const checked = [...radios].find(r => r.checked);
                    const val = checked ? parseInt(checked.value) : 0;
                    stars.forEach((s, i) => { s.style.color = (i < val) ? '#ffc107' : '#ccc'; });
                    if (label) label.textContent = val ? labels[val - 1] : "Choose rating";
                });
                star.addEventListener('click', () => { radios[value - 1].checked = true; });
            });
        });

        // terms / register button
        document.addEventListener("DOMContentLoaded", function () {
            const checkbox = document.getElementById("terms");
            const registerBtn = document.getElementById("registerButton");
            if (checkbox && registerBtn) {
                registerBtn.disabled = !checkbox.checked;
                checkbox.addEventListener("change", function () {
                    registerBtn.disabled = !this.checked;
                    if (this.checked) {
                        registerBtn.classList.remove("btn-secondary");
                        registerBtn.classList.add("btn-danger");
                    } else {
                        registerBtn.classList.remove("btn-danger");
                        registerBtn.classList.add("btn-secondary");
                    }
                });
            }
        });
    </script>

    <!-- 🚀 Custom controller for TOP tiles (placed AFTER jQuery & Bootstrap) -->
    <script>
      $(function () {
        const $topLinks   = $('#profileTopTabs .nav-link');
        const $topContent = $('.profile-main > .tab-content').first(); // top-level panes container

        // Unhook Bootstrap for top tiles so it doesn't fight our logic
        $topLinks.each(function(){
          $(this).off('click.bs.tab'); // remove BS tab handler if attached
        });
        $topLinks.removeAttr('data-toggle').removeAttr('data-bs-toggle');

        function showTopPane(href) {
          const target = href || '#user-dashboard';

          // Active state on tiles
          $topLinks.removeClass('active');
          $topLinks.filter('[href="' + target + '"]').addClass('active');

          // Show only the chosen top-level pane
          $topContent.children('.tab-pane').removeClass('show active');
          $(target).addClass('show active');

          // Show orders subtabs only for "Your Orders"
          $('#orders-subtabs').toggle(target === '#user-dashboard');
        }

        // Click handler for the tiles
        $('#profileTopTabs').on('click', '.nav-link', function (e) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          showTopPane($(this).attr('href'));
        });

        // Initialize on load
        const initialHref = $topLinks.filter('.active').attr('href') || '#user-dashboard';
        showTopPane(initialHref);
      });
    </script>

</x-app-layout>
