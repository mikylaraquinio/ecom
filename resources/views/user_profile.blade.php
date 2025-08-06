<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
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
                        document.getElementById('profilePictureInput').addEventListener('change', function (event) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    document.getElementById('profilePicturePreview').src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    </script>

                    <!-- FontAwesome for the Pen Icon -->
                    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

                    <ul class="nav flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="#user-dashboard" data-toggle="pill">
                                <i class="fas fa-seedling me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#wishlist-section" data-toggle="pill">
                                <i class="fas fa-heart me-2"></i> Wishlist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-general" data-toggle="pill">
                                <i class="fas fa-leaf me-2"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-change-password" data-toggle="pill">
                                <i class="fas fa-lock me-2"></i> Change Password
                            </a>
                        </li>

                        @if(auth()->user()->role !== 'seller')
                            <div class="text-center mt-3">
                            <a href="#" class="btn btn-success shadow-sm rounded-pill px-4 d-flex align-items-center justify-content-center"
   data-bs-toggle="modal" data-bs-target="#ModalCreate">
    <i class="fas fa-tractor me-2"></i> Start Selling
</a>

                            </div>
                        @else
                            <div class="text-center mt-3">
                                <a href="{{ route('myshop') }}" class="btn text-white shadow-sm rounded-pill px-4 d-flex align-items-center justify-content-center" style="background-color: #8B5E3C;">
                                    <i class="fas fa-basket me-2"></i> My Shop
                                </a>
                            </div>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="user-dashboard">
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

                        <div class="tab-content mt-2">
                            <!-- To Ship -->
                            <div class="tab-pane fade show active" id="to-ship">
                                @if($ordersToShip->isEmpty())
                                    <p>No orders to ship.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($ordersToShip as $order)
                                            @foreach($order->orderItems as $orderItem)
                                                @php
                                                    $product = $orderItem->product;
                                                    $imageUrl = $product && $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('assets/products.jpg');
                                                @endphp

                                                @if($product)
                                                    <div class="col">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    <img src="{{ $imageUrl }}"
                                                                        class="img-fluid rounded-start p-2 object-fit-cover"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body p-2">
                                                                        <h6 class="card-title text-truncate">
                                                                            {{ $product->name }}
                                                                        </h6>
                                                                        <p class="small mb-1"><strong>Price:</strong>
                                                                            ${{ number_format($product->price, 2) }}</p>
                                                                        <p class="small mb-1"><strong>Qty:</strong>
                                                                            {{ $orderItem->quantity }}</p>
                                                                        <p class="small mb-2"><strong>Stock:</strong>
                                                                            {{ $product->stock }}</p>

                                                                        <!-- Order Status Badge -->
                                                                        <span class="badge 
                                                                    {{ $order->status === 'pending' ? 'bg-warning' : 'bg-success' }}">
                                                                            {{ $order->status === 'pending' ? 'Pending' : 'Ready to Ship' }}
                                                                        </span>

                                                                        <!-- Cancel Order Button (Only for Pending or Accepted) -->
                                                                        @if($order->status === 'pending' || $order->status === 'accepted')
                                                                            <form action="{{ route('buyer.cancelOrder', $order->id) }}"
                                                                                method="POST" style="display:inline;">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <input type="hidden" name="status" value="canceled">
                                                                                <button type="submit" class="btn btn-danger btn-sm mt-2">
                                                                                    Cancel
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="alert alert-danger small p-2 text-center">
                                                            Product information not available
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <!-- To Receive -->
                            <div class="tab-pane fade" id="to-receive">
                                @if($ordersToReceive->isEmpty())
                                    <p>No orders to receive.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($ordersToReceive as $order)
                                            @foreach($order->orderItems as $orderItem)
                                                @php
                                                    $product = $orderItem->product;
                                                    $imageUrl = $product && $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('assets/products.jpg');
                                                @endphp

                                                @if($product)
                                                    <div class="col">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    <img src="{{ $imageUrl }}"
                                                                        class="img-fluid rounded-start p-2 object-fit-cover"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body p-2">
                                                                        <h6 class="card-title text-truncate">
                                                                            {{ $product->name }}
                                                                        </h6>
                                                                        <p class="small mb-1"><strong>Price:</strong>
                                                                            ${{ number_format($product->price, 2) }}</p>
                                                                        <p class="small mb-1"><strong>Qty:</strong>
                                                                            {{ $orderItem->quantity }}</p>
                                                                        <p class="small mb-2"><strong>Stock:</strong>
                                                                            {{ $product->stock }}</p>
                                                                        <span class="badge bg-info">
                                                                            Shipped(To Receive)
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="alert alert-danger small p-2 text-center">
                                                            Product information not available
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- To Review -->
                            <div class="tab-pane fade" id="to-review">
                                @if($ordersToReview->isEmpty())
                                    <p>No orders to review.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($ordersToReview as $order)
                                            @foreach($order->orderItems as $orderItem)
                                                @php
                                                    $product = $orderItem->product;
                                                    $seller = $product->seller ?? null;
                                                    $imageUrl = $product && $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('assets/products.jpg');
                                                    $hasReviewed = $orderItem->review;
                                                @endphp

                                                @if($product)
                                                    <div class="col">
                                                        <div class="card shadow-sm h-100">
                                                            <!-- Shop Header -->
                                                            <div class="card-header bg-light px-2 py-1 border-bottom">
                                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                                                    <!-- Seller Name -->
                                                                    <strong class="me-auto">{{ $seller->farm_name ?? 'Unknown Shop' }}</strong>

                                                                    <!-- Buttons & Badge -->
                                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                        <a class="btn btn-xs btn-outline-success py-0 px-1"
                                                                        style="font-size: 0.75rem; pointer-events: none; opacity: 0.6;" 
                                                                        title="Coming soon">Chat</a>

                                                                        <a class="btn btn-xs btn-outline-primary py-0 px-1"
                                                                        style="font-size: 0.75rem; pointer-events: none; opacity: 0.6;" 
                                                                        title="Coming soon">Visit Shop</a>

                                                                        <span class="badge bg-success" style="font-size: 0.7rem;">Completed</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Product Content -->
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    <img src="{{ $imageUrl }}"
                                                                        class="img-fluid rounded-start p-2"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body p-2">
                                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                                            <h6 class="card-title text-truncate mb-0">{{ $product->name }}</h6>
                                                                        </div>
                                                                        <p class="small mb-1"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                                                        <p class="small mb-1"><strong>Qty:</strong> {{ $orderItem->quantity }}</p>
                                                                        <p class="small mb-2"><strong>Stock:</strong> {{ $product->stock }}</p>

                                                                        @if($hasReviewed)
                                                                            <span class="badge bg-secondary">Reviewed</span>
                                                                        @else
                                                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#rateModal-{{ $orderItem->id }}">
                                                                                Rate
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                                    <label for="comment" class="form-label">Write a Review</label>
                                                                    <textarea class="form-control" name="review" rows="3" placeholder="Share your thoughts..." required></textarea>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="photo" class="form-label">Add Photo (optional)</label>
                                                                    <input type="file" class="form-control" name="photo" accept="image/*">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="video" class="form-label">Add Video (optional)</label>
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
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Submit Review</button>
                                                                </div>
                                                            </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="alert alert-danger small p-2 text-center">
                                                            Product information not available
                                                        </div>
                                                    </div>


                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

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

                </div>
            </main>
        </div>
    </div>

    <style>
    .stars {
        display: flex;
        direction: ltr;
    }

    .stars label {
        font-size: 2rem;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
        margin-right: 5px;
    }

    .stars input[type="radio"] {
        display: none;
    }

    .stars input[type="radio"]:checked ~ label {
        color: #ccc; /* Don't highlight here; JS will handle it */
    }

    .stars label:hover,
    .stars label:hover ~ label {
        color: #ffc107;
    }
    </style>


    <script>
        function uploadProfilePicture(event) {
            let file = event.target.files[0];
            if (file) {
                let formData = new FormData();
                formData.append('profile_picture', file);

                fetch({
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json())
                    .then(data => location.reload())
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>


<!-- JavaScript for AJAX Form Submission -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("sellerRegistrationForm");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent page refresh

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
                // Close modal
                let modal = bootstrap.Modal.getInstance(document.getElementById("ModalCreate"));
                modal.hide();

                // Replace the button with "My Shop"
                document.getElementById("shopButtonContainer").innerHTML = `
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
});
</script>

    <script>
        // Handle "Add to Cart" with AJAX
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent normal form submission

                const productId = this.dataset.id;
                const quantity = 1; // default quantity if not using input field

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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!',
                    });
                    console.error(err);
                });
            });
        });

        // Handle "Remove from Wishlist"
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not remove item from wishlist.',
                    });
                    console.error(err);
                });
            });
        });
    </script>

    <script>
    document.querySelectorAll('.stars').forEach(starGroup => {
        const orderId = starGroup.getAttribute('data-order-id');
        const label = document.getElementById(`rating-label-${orderId}`);
        const stars = starGroup.querySelectorAll('label');
        const radios = starGroup.querySelectorAll('input[type="radio"]');
        const labels = ["Terrible", "Poor", "Average", "Good", "Amazing"];

        // Highlight stars on hover
        stars.forEach((star, idx) => {
            const value = parseInt(star.getAttribute('data-value'));

            star.addEventListener('mouseenter', () => {
                stars.forEach((s, i) => {
                    s.style.color = (i < value) ? '#ffc107' : '#ccc';
                });
                label.textContent = labels[value - 1];
            });

            star.addEventListener('mouseleave', () => {
                const checked = [...radios].find(r => r.checked);
                const val = checked ? parseInt(checked.value) : 0;

                stars.forEach((s, i) => {
                    s.style.color = (i < val) ? '#ffc107' : '#ccc';
                });

                label.textContent = val ? labels[val - 1] : "Choose rating";
            });

            star.addEventListener('click', () => {
                radios[value - 1].checked = true;
            });
        });
    });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    @include('farmers.modal.sell')
</x-app-layout>