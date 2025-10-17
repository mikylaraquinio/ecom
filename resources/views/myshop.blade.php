<x-app-layout>
  <div class="container py-4">
    <div class="bg-white border rounded shadow-sm p-3 mb-3">
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle border" style="width:56px;height:56px;"></div>
          <div>
            <div class="fw-bold fs-5">{{ auth()->user()->farm_name ?? 'Shop name' }}</div>
            <div class="text-muted small">{{ auth()->user()->email ?? 'sellers email' }}</div>
          </div>
        </div>
        <a href="{{ route('shop') }}" class="btn btn-outline-dark btn-sm fw-semibold">View Shop</a>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-lg-6">
        <div class="bg-white border rounded shadow-sm p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Order Status</div>
            <a class="text-decoration-none small fw-semibold" href="#analytics" data-target="analytics">View Sales
              History ></a>
          </div>

          <div class="d-flex flex-wrap gap-2">
            <a href="{{ url()->current() }}?status=pending#order-status" class="text-decoration-none">
              <div class="border rounded p-2 text-center" style="width:110px;">
                <div class="fw-bold">0</div>
                <div class="small text-muted">To ship</div>
              </div>
            </a>
            <a href="{{ url()->current() }}?status=canceled#order-status" class="text-decoration-none">
              <div class="border rounded p-2 text-center" style="width:110px;">
                <div class="fw-bold">0</div>
                <div class="small text-muted">Cancelled</div>
              </div>
            </a>
            <a href="{{ url()->current() }}?status=denied#order-status" class="text-decoration-none">
              <div class="border rounded p-2 text-center" style="width:110px;">
                <div class="fw-bold">0</div>
                <div class="small text-muted">Return</div>
              </div>
            </a>
            <a href="{{ url()->current() }}?status=completed#order-status" class="text-decoration-none">
              <div class="border rounded p-2 text-center" style="width:110px;">
                <div class="fw-bold">0</div>
                <div class="small text-muted">Review</div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-3 d-flex">
        <div class="bg-white border rounded shadow-sm p-3 flex-fill">
          <div class="d-flex flex-nowrap gap-2 overflow-auto actions-line">
            <a href="#order-status" data-target="order-status" class="nav-link text-decoration-none flex-shrink-0">
              <div
                class="action-mini border rounded text-center d-flex flex-column align-items-center justify-content-center py-2 px-2"
                style="width:110px;">
                <i class="fas fa-seedling"></i>
                <div class="small fw-semibold mt-1">Order Status</div>
              </div>
            </a>

            <a href="#my-shop" data-target="my-shop" class="nav-link text-decoration-none flex-shrink-0">
              <div
                class="action-mini border rounded text-center d-flex flex-column align-items-center justify-content-center py-2 px-2"
                style="width:110px;">
                <i class="fas fa-store-alt"></i>
                <div class="small fw-semibold mt-1">Products</div>
              </div>
            </a>

            <a href="#analytics" data-target="analytics" class="nav-link text-decoration-none flex-shrink-0">
              <div
                class="action-mini border rounded text-center d-flex flex-column align-items-center justify-content-center py-2 px-2"
                style="width:110px;">
                <i class="fas fa-chart-line"></i>
                <div class="small fw-semibold mt-1">Analytics</div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <style>
        .action-mini {
          transition: transform .12s ease, box-shadow .12s ease;
          min-height: 84px;
        }

        .action-mini:hover {
          transform: translateY(-1px);
          box-shadow: 0 .35rem .8rem rgba(0, 0, 0, .06);
        }

        .action-mini i {
          font-size: 1rem;
          line-height: 1;
        }

        .action-mini .small {
          font-size: .8rem;
        }

        .actions-line {
          -webkit-overflow-scrolling: touch;
        }

        /* smooth scroll on mobile */
      </style>
    </div>

    <div class="bg-white border rounded shadow-sm p-3">
      <div class="tab-content">
        @if(auth()->check() && auth()->user()->role === 'seller')
            <div class="tab-pane fade show active" id="order-status">
              {{-- Header: centered title + filter on the right --}}
              <div class="mb-2 d-grid" style="grid-template-columns: 1fr auto 1fr; align-items: center;">
                <div></div>
                <div class="text-center">
                  <h5 class="fw-bold mb-0">Your Orders</h5>
                </div>
                <div class="d-flex justify-content-end">
                  <form method="GET" action="{{ url()->current() }}#order-status" class="d-flex align-items-center gap-2">
                    @foreach(request()->except('status') as $key => $val)
                      @if(is_array($val))
                        @foreach($val as $v)
                          <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                      @else
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                      @endif
                    @endforeach
                    <label class="small text-muted mb-0">Filter:</label>
                    <select name="status" class="form-select form-select-sm" style="max-width: 220px;"
                      onchange="this.form.submit()">
                      <option value="">All statuses</option>
                      <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                      <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped / Ready for
                        Pickup</option>
                      <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                      <option value="cancel_requested" {{ request('status') === 'cancel_requested' ? 'selected' : '' }}>Cancel
                        Requested</option>
                      <option value="denied" {{ request('status') === 'denied' ? 'selected' : '' }}>Denied</option>
                      <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                    </select>
                  </form>
                </div>
              </div>

              <div class="table-responsive shadow-sm rounded bg-white p-3"
                style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd;">
                @if(isset($orders) && $orders->count() > 0)
                  <table class="table text-center">
                    <thead class="table-dark">
                      <tr>
                        <th>Order ID</th>
                        <th>Buyer</th>
                        <th>Fulfillment</th>
                        <th>Shipping / Pickup Details</th>
                        <th>Products</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($orders as $order)
                        @php $method = $order->fulfillment_method === 'pickup' ? 'pickup' : 'delivery'; @endphp
                        <tr>
                          <td>{{ $order->id }}</td>
                          <td>{{ $order->buyer->name }}</td>
                          <td>
                            <span class="badge rounded-pill {{ $method === 'pickup' ? 'bg-warning text-dark' : 'bg-primary' }}">
                              {{ $method === 'pickup' ? 'Pick up' : 'For delivery' }}
                            </span>
                          </td>
                          <td class="text-start">
                            @if($method === 'pickup')
                                        <div class="fw-semibold">Pickup Location</div>
                                        <div class="text-muted small">
                                          {{ auth()->user()->farm_address
                              ?? trim((auth()->user()->address_line1 ?? '') . ' ' . (auth()->user()->barangay ?? '') . ', ' . (auth()->user()->city ?? '') . ', ' . (auth()->user()->province ?? ''))
                              ?: 'Seller will coordinate pickup details with buyer' }}
                                        </div>
                                        <div class="small mt-1">
                                          <i class="bi bi-info-circle"></i>
                                          No shipping fee. Mark as “Ready for Pickup” when order is prepared.
                                        </div>
                            @else
                              @if($order->shippingAddress)
                                <div class="fw-semibold">
                                  {{ $order->shippingAddress->full_name }} — {{ $order->shippingAddress->mobile_number }}
                                </div>
                                <div class="text-muted small">
                                  @if($order->shippingAddress->floor_unit_number)
                                    {{ $order->shippingAddress->floor_unit_number }},
                                  @endif
                                  {{ $order->shippingAddress->barangay }},
                                  {{ $order->shippingAddress->city }},
                                  {{ $order->shippingAddress->province }}
                                </div>
                                @if($order->shippingAddress->notes)
                                  <div class="text-muted fst-italic small">{{ $order->shippingAddress->notes }}</div>
                                @endif
                              @else
                                <span class="text-danger">No Shipping Address Provided</span>
                              @endif
                            @endif
                          </td>
                          <td class="text-start">
                            <ul class="list-unstyled mb-0 small">
                              @foreach($order->orderItems as $item)
                                <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                              @endforeach
                            </ul>
                          </td>
                          <td>₱{{ number_format($order->total_amount, 2) }}</td>
                          <td>
                            <span
                              class="badge text-white
                                                                                                                                                                    @if($order->status == 'pending') bg-warning text-dark
                                                                                                                                                                    @elseif($order->status == 'accepted') bg-success
                                                                                                                                                                    @elseif($order->status == 'denied' || $order->status == 'canceled') bg-danger
                                                                                                                                                                    @elseif($order->status == 'shipped') bg-primary
                                                                                                                                                                    @elseif($order->status == 'completed') bg-info
                                                                                                                                                                    @elseif($order->status == 'cancel_requested') bg-warning
                                                                                                                                                                    @else bg-secondary @endif">
                              {{ ucfirst($order->status) }}
                            </span>
                          </td>
                          <td class="text-center">
                            @if($order->status == 'pending')
                              <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Accept this order?')">
                                  @csrf @method('PATCH')
                                  <input type="hidden" name="status" value="accepted">
                                  <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Deny this order?')">
                                  @csrf @method('PATCH')
                                  <input type="hidden" name="status" value="denied">
                                  <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                </form>
                              </div>
                            @elseif($order->status == 'accepted')
                              <div class="d-flex justify-content-center">
                                <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                  @csrf @method('PATCH')
                                  <input type="hidden" name="status" value="shipped">
                                  <button type="submit" class="btn btn-primary btn-sm">
                                    {{ $method === 'pickup' ? 'Mark Ready for Pickup' : 'Mark as Shipped' }}
                                  </button>
                                </form>
                              </div>
                            @elseif($order->status == 'shipped')
                              <div class="d-flex justify-content-center">
                                <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                  @csrf @method('PATCH')
                                  <input type="hidden" name="status" value="completed">
                                  <button type="submit" class="btn btn-success btn-sm">Mark as Completed</button>
                                </form>
                              </div>
                            @elseif($order->status == 'canceled')
                              <span class="badge bg-danger">Canceled by Buyer</span>
                            @elseif($order->status == 'cancel_requested')
                              <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('seller.approveCancel', $order->id) }}" method="POST">
                                  @csrf @method('PATCH')
                                  <button type="submit" class="btn btn-danger btn-sm">Approve Cancellation</button>
                                </form>
                                <form action="{{ route('seller.denyCancel', $order->id) }}" method="POST">
                                  @csrf @method('PATCH')
                                  <button type="submit" class="btn btn-success btn-sm">Deny Cancellation</button>
                                </form>
                              </div>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <div class="d-flex justify-content-center">
                    {{ $orders->links('pagination::bootstrap-5') }}
                  </div>
                @else
                  <p class="text-center text-muted">No orders available.</p>
                @endif
              </div>
            </div>

            {{-- ========== My Shop (UNCHANGED LOGIC) ========== --}}
            <div class="tab-pane fade" id="my-shop">
              <div class="shop-header bg-white shadow-sm p-1 rounded">
                <h4 class="fw-bold mb-1">My Products</h4>
              </div>

              <div class="pt-2">
                <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
                  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Product
                  </button>
                </div>

                @php
                  $products = \App\Models\Product::where('user_id', auth()->id())->get();
                @endphp

                @if($products->isNotEmpty())
                  {{-- Toolbar --}}
                  <form method="GET" action="{{ url()->current() }}#my-shop" class="bg-white border rounded p-2 mb-2">
                    <div class="row g-2 align-items-center">
                      <div class="col-12 col-md-6 col-lg-5">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                          placeholder="Search Product Name, ID…">
                      </div>
                      <div class="col-6 col-md-3 col-lg-3">
                        <select name="category" class="form-select">
                          <option value="">Category</option>
                          @foreach($mainCategories as $category)
                            <option value="{{ $category->id }}" {{ (string) request('category') === (string) $category->id ? 'selected' : '' }}>
                              {{ $category->name }}
                            </option>
                            @foreach($category->subcategories as $sub)
                              <option value="{{ $sub->id }}" {{ (string) request('category') === (string) $sub->id ? 'selected' : '' }}>
                                └ {{ $sub->name }}
                              </option>
                            @endforeach
                          @endforeach
                        </select>
                      </div>
                      <div class="col-12 col-md-12 col-lg-2 d-flex gap-2 justify-content-start justify-content-lg-end">
                        <button class="btn btn-outline-danger">Apply</button>
                        <a href="{{ url()->current() }}#my-shop" class="btn btn-outline-secondary">Reset</a>
                      </div>
                    </div>
                  </form>

                  {{-- Tabs header --}}
                  <ul class="nav nav-underline small mb-2">
                    <li class="nav-item"><span class="nav-link active">All</span></li>
                    <li class="nav-item"><span class="nav-link text-muted">Restock (0)</span></li>
                    <li class="nav-item"><span class="nav-link text-muted">To Review Listing Detail (1)</span></li>
                  </ul>

                  {{-- List --}}
                  <div class="product-list card border-0">
                    <div class="table-responsive">
                      <table class="table align-middle mb-0">
                        <thead class="small text-muted">
                          <tr>
                            <th style="width:44px;">
                              <input class="form-check-input" type="checkbox" id="selectAllProducts"
                                onclick="document.querySelectorAll('.row-check').forEach(cb=>cb.checked=this.checked)">
                            </th>
                            <th>Product(s)</th>
                            <th class="text-center" style="width:110px;">Sales</th>
                            <th class="text-end" style="width:120px;">Price</th>
                            <th class="text-center" style="width:110px;">Stock</th>
                            <th class="text-start" style="width:160px;">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($products as $product)
                            @php
                              $thumb = asset('storage/' . $product->image);
                              $sales = $product->sales_count ?? 0;   // replace with your metric
                              $issues = 1;                           // replace with your quality logic
                            @endphp
                            <tr class="product-row">
                              <td><input class="form-check-input row-check" type="checkbox"></td>

                              <td>
                                <div class="d-flex align-items-start gap-2">
                                  <img src="{{ $thumb }}" alt="{{ $product->name }}" class="rounded border"
                                    style="width:64px;height:64px;object-fit:cover;">
                                  <div>
                                    <div class="fw-semibold text-truncate" style="max-width: 360px;">
                                      <a href="#" class="text-decoration-none link-dark" data-bs-toggle="modal"
                                        data-bs-target="#viewProductModal{{ $product->id }}">
                                        {{ $product->name }}
                                      </a>
                                    </div>
                                    <div class="text-muted xsmall">Item ID: <span
                                        class="text-body-secondary">{{ $product->id }}</span></div>
                                  </div>
                                </div>
                              </td>

                              <td class="text-center text-muted">{{ $sales }}</td>
                              <td class="text-end">₱{{ number_format($product->price, 2) }}</td>
                              <td class="text-center">{{ $product->stock }}</td>

                              <td class="text-start">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                  {{-- EDIT uses your modal below --}}
                                  <a href="#" class="link-primary small text-decoration-none" data-bs-toggle="modal"
                                    data-bs-target="#editProductModal{{ $product->id }}">Edit</a>


                                  <div class="dropstart">
                                    <a href="#" class="link-secondary small text-decoration-none"
                                      data-bs-toggle="dropdown">More</a>
                                    <ul class="dropdown-menu shadow-sm">
                                      <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                          data-bs-target="#viewProductModal{{ $product->id }}">
                                          <i class="fas fa-eye me-2"></i> View
                                        </a>
                                      </li>
                                      <li>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                          data-bs-target="#confirmDelete{{ $product->id }}">
                                          <i class="fas fa-trash me-2"></i> Delete
                                        </a>
                                      </li>
                                    </ul>
                                  </div>
                                </div>
                              </td>
                            </tr>

                            {{-- View Product Modal (aesthetic, mirrors Add/Edit fields) --}}
                            @php
                              $viewSlides = [];
                              if (!empty($product->image))
                                $viewSlides[] = asset('storage/' . $product->image);
                              if (isset($product->images) && $product->images->count()) {
                                foreach ($product->images as $img) {
                                  $viewSlides[] = asset('storage/' . $img->path);
                                }
                              }
                            @endphp
                            <div class="modal fade" id="viewProductModal{{ $product->id }}" tabindex="-1"
                              aria-labelledby="viewProductModalLabel{{ $product->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0">
                                  <div class="modal-header border-0 pb-0">
                                    <div>
                                      <h5 class="modal-title fw-bold" id="viewProductModalLabel{{ $product->id }}">
                                        <i class="fas fa-box-open me-2"></i>{{ $product->name }}
                                      </h5>
                                      <div class="text-muted small">Product preview and details</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>

                                  <div class="modal-body pt-2">
                                    <div class="row g-3">
                                      {{-- LEFT: images / preview --}}
                                      <div class="col-lg-4">
                                        <div class="border rounded-3 p-3 h-100">
                                          <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-bold">Images</div>
                                            <span class="badge bg-light text-dark">{{ count($viewSlides) }}/9</span>
                                          </div>

                                          <div id="viewCarousel{{ $product->id }}" class="carousel slide mb-2"
                                            data-bs-touch="true">
                                            <div class="carousel-inner ratio ratio-1x1 border rounded bg-light overflow-hidden">
                                              @if(count($viewSlides))
                                                @foreach($viewSlides as $i => $src)
                                                  <div class="carousel-item @if($i === 0) active @endif">
                                                    <img src="{{ $src }}" class="d-block w-100 h-100" style="object-fit:cover;"
                                                      alt="Image {{ $i + 1 }}">
                                                  </div>
                                                @endforeach
                                              @else
                                                <div
                                                  class="carousel-item active d-flex align-items-center justify-content-center">
                                                  <span class="text-muted">No image</span>
                                                </div>
                                              @endif
                                            </div>
                                            <button class="carousel-control-prev" type="button"
                                              data-bs-target="#viewCarousel{{ $product->id }}" data-bs-slide="prev">
                                              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                              <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button"
                                              data-bs-target="#viewCarousel{{ $product->id }}" data-bs-slide="next">
                                              <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                              <span class="visually-hidden">Next</span>
                                            </button>
                                          </div>

                                          <div class="small">
                                            <div class="text-muted mb-1">Shop</div>
                                            <div class="form-control form-control-sm bg-light fw-semibold" readonly>
                                              {{ auth()->user()->farm_name ?? 'Shop Name' }}
                                            </div>
                                          </div>
                                        </div>
                                      </div>

                                      {{-- RIGHT: details (read-only, no cards/inputs) --}}
                                      <div class="col-lg-8">
                                        <div class="px-lg-1">
                                          <h6 class="fw-bold mb-3">Basic information</h6>

                                          {{-- Product name --}}
                                          <div class="mb-2">
                                            <div class="text-muted small mb-1">Product Name</div>
                                            <div class="fs-6 fw-semibold">{{ $product->name }}</div>
                                          </div>

                                          {{-- Description --}}
                                          <div class="mb-3">
                                            <div class="text-muted small mb-1">Description</div>
                                            <div class="text-body" style="white-space:pre-wrap">
                                              {{ $product->description ?? '—' }}
                                            </div>
                                          </div>

                                          {{-- Key facts in two columns --}}
                                          <dl class="row small mb-0 view-specs">
                                            <dt class="col-sm-4 text-muted">Price</dt>
                                            <dd class="col-sm-8 fw-semibold">₱{{ number_format($product->price, 2) }}</dd>

                                            <dt class="col-sm-4 text-muted">Unit</dt>
                                            <dd class="col-sm-8">{{ $product->unit ?? '—' }}</dd>

                                            <dt class="col-sm-4 text-muted">Stock</dt>
                                            <dd class="col-sm-8">{{ $product->stock }}</dd>

                                            <dt class="col-sm-4 text-muted">Minimum Order Qty</dt>
                                            <dd class="col-sm-8">{{ $product->min_order_qty ?? '—' }}</dd>

                                            <dt class="col-sm-4 text-muted">Weight / unit</dt>
                                            <dd class="col-sm-8">
                                              {{ isset($product->weight) ? number_format($product->weight, 2) . ' kg' : '—' }}
                                              <span class="text-muted">· example: 1 mango ≈ 0.25 kg</span>
                                            </dd>

                                            <dt class="col-sm-4 text-muted">Category</dt>
                                            <dd class="col-sm-8">{{ $product->category->name ?? 'Uncategorized' }}</dd>

                                            <dt class="col-sm-4 text-muted">Shop</dt>
                                            <dd class="col-sm-8">{{ auth()->user()->farm_name ?? 'Shop Name' }}</dd>
                                          </dl>
                                        </div>
                                      </div>

                                    </div> {{-- /row --}}
                                  </div>

                                  <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-outline-secondary"
                                      data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                      data-bs-target="#editProductModal{{ $product->id }}" data-bs-dismiss="modal">
                                      <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>


                            {{-- ===== YOUR Edit Product Modal (two-column, scrollable, swipeable) ===== --}}
                            @php
                              $existingSlides = [];
                              if (!empty($product->image))
                                $existingSlides[] = asset('storage/' . $product->image);
                              if (isset($product->images) && $product->images->count()) {
                                foreach ($product->images as $img) {
                                  $existingSlides[] = asset('storage/' . $img->path);
                                }
                              }
                            @endphp
                            <div class="modal fade edit-product-modal" id="editProductModal{{ $product->id }}"
                              data-pid="{{ $product->id }}" tabindex="-1" aria-labelledby="editProductLabel{{ $product->id }}"
                              aria-hidden="true">
                              <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                  <div class="modal-header border-0 pb-0">
                                    <div>
                                      <h5 class="modal-title fw-bold" id="editProductLabel{{ $product->id }}">
                                        <i class="fas fa-edit me-2"></i> Edit Product
                                      </h5>
                                      <div class="text-muted small">Update details on the right and preview on the left.</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>

                                  <form id="updateProductForm" action="{{ route('products.update', $product->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')


                                    <div class="modal-body pt-2">
                                      <div class="row g-3">
                                        {{-- LEFT: Preview --}}
                                        <div class="col-lg-4">
                                          <div class="border rounded-3 p-3 h-100">
                                            <div class="fw-bold">Preview</div>
                                            <div class="text-muted small mb-2">Product detail</div>

                                            <div id="editPreviewCarousel{{ $product->id }}" class="carousel slide mb-2"
                                              data-bs-touch="true">
                                              <div
                                                class="carousel-inner ratio ratio-1x1 border rounded bg-light overflow-hidden"
                                                id="editCarouselInner{{ $product->id }}">
                                                @if(count($existingSlides))
                                                  @foreach($existingSlides as $idx => $src)
                                                    <div class="carousel-item @if($idx === 0) active @endif">
                                                      <img src="{{ $src }}" class="d-block w-100 h-100" style="object-fit:cover;"
                                                        alt="Image {{ $idx + 1 }}">
                                                    </div>
                                                  @endforeach
                                                @else
                                                  <div
                                                    class="carousel-item active d-flex align-items-center justify-content-center">
                                                    <span class="text-muted">No image yet</span>
                                                  </div>
                                                @endif
                                              </div>
                                              <button class="carousel-control-prev" type="button"
                                                data-bs-target="#editPreviewCarousel{{ $product->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                              </button>
                                              <button class="carousel-control-next" type="button"
                                                data-bs-target="#editPreviewCarousel{{ $product->id }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                              </button>
                                            </div>

                                            <div class="mb-3">
                                              <div class="form-control form-control-sm bg-light fw-semibold" readonly>
                                                {{ auth()->user()->farm_name ?? 'Shop Name' }}
                                              </div>
                                            </div>

                                            <div>
                                              <div class="fw-bold mb-2">Details</div>
                                              <div class="small">
                                                <div class="d-flex justify-content-between py-1 border-bottom">
                                                  <span class="text-muted">Product:</span>
                                                  <span id="pv_name_{{ $product->id }}"
                                                    class="fw-semibold">{{ $product->name }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-1 border-bottom">
                                                  <span class="text-muted">Price:</span>
                                                  <span id="pv_price_{{ $product->id }}"
                                                    class="fw-semibold">₱{{ number_format($product->price, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-1 border-bottom">
                                                  <span class="text-muted">Unit:</span>
                                                  <span id="pv_unit_{{ $product->id }}"
                                                    class="fw-semibold">{{ $product->unit ?? '—' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-1 border-bottom">
                                                  <span class="text-muted">Stock:</span>
                                                  <span id="pv_stock_{{ $product->id }}"
                                                    class="fw-semibold">{{ $product->stock }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-1 border-bottom">
                                                  <span class="text-muted">Min order qty:</span>
                                                  <span id="pv_moq_{{ $product->id }}"
                                                    class="fw-semibold">{{ $product->min_order_qty ?? '—' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between py-1">
                                                  <span class="text-muted">Weight / unit:</span>
                                                  <span id="pv_weight_{{ $product->id }}" class="fw-semibold">
                                                    @if(isset($product->weight)) {{ number_format($product->weight, 2) }} kg
                                                    @else — @endif
                                                  </span>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                        {{-- RIGHT: Basic info --}}
                                        <div class="col-lg-8">
                                          <div class="px-lg-1">
                                            <div class="fw-bold">Basic Information</div>

                                            {{-- Shopee-style Image Uploader --}}
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                              <div class="fw-semibold">Product images</div>
                                              <div class="small text-muted">
                                                <span id="shpCount{{ $product->id }}">{{ count($existingSlides) }}</span>/9
                                              </div>
                                            </div>

                                            <div id="shpGrid{{ $product->id }}" class="shp-grid">
                                              {{-- Existing images as tiles (cover = first) --}}
                                              @php
                                                $existingList = [];
                                                // main image (no ID assumed)
                                                if (!empty($product->image)) {
                                                  $existingList[] = ['id' => null, 'src' => asset('storage/' . $product->image)];
                                                }
                                                // gallery images with IDs
                                                if (isset($product->images) && $product->images->count()) {
                                                  foreach ($product->images as $img) {
                                                    $existingList[] = ['id' => $img->id, 'src' => asset('storage/' . $img->path)];
                                                  }
                                                }
                                              @endphp

                                              @foreach($existingList as $idx => $img)
                                                <div class="shp-item" draggable="true" data-type="existing" @if($img['id'])
                                                data-existing-id="{{ $img['id'] }}" @endif>
                                                  <img src="{{ $img['src'] }}" alt="Image {{ $idx + 1 }}">
                                                  <button type="button" class="shp-remove" aria-label="Remove">&times;</button>
                                                  @if($idx === 0)
                                                    <span class="shp-cover">Cover</span>
                                                  @endif
                                                </div>
                                              @endforeach

                                              {{-- Add tile --}}
                                              <label class="shp-add">
                                                <input id="shpPicker{{ $product->id }}" type="file" accept="image/*" multiple
                                                  hidden>
                                                <div class="shp-add-inner">
                                                  <div class="shp-plus">+</div>
                                                  <div class="small text-muted">Add</div>
                                                </div>
                                              </label>
                                            </div>

                                            {{-- Hidden mapping to your backend --}}
                                            <input id="shpMainFile{{ $product->id }}" name="image" type="file" class="d-none">
                                            {{-- main (first new file) --}}
                                            <input id="shpGalleryFiles{{ $product->id }}" name="gallery[]" type="file"
                                              class="d-none" multiple> {{-- remaining new files --}}
                                            {{-- Track existing re-order (IDs or "main") and removals --}}
                                            <div id="shpExistingWrap{{ $product->id }}"></div> {{-- will be filled with
                                            existing_order[] and remove_existing[] --}}
                                            <input type="hidden" name="remove_existing" class="removeExistingInput" value="[]">
                                            <div class="form-text">Up to 9 images. Drag to reorder. First image is the Cover.
                                            </div>


                                            <label class="form-label fw-semibold">Product Name <span
                                                class="text-danger">*</span></label>
                                            <input id="fld_name_{{ $product->id }}" type="text" class="form-control" name="name"
                                              value="{{ $product->name }}" maxlength="120" required>
                                            <div class="invalid-feedback">Product name is required.</div>

                                            <label class="form-label fw-semibold mt-3">Description <span
                                                class="text-danger">*</span></label>
                                            <textarea id="fld_desc_{{ $product->id }}" class="form-control" name="description"
                                              rows="4" maxlength="800" required>{{ $product->description }}</textarea>
                                            <div class="invalid-feedback">Please add a short description.</div>

                                            <div class="row g-3 mt-1">
                                              <div class="col-md-4">
                                                <label class="form-label fw-semibold">Price <span
                                                    class="text-danger">*</span></label>
                                                <div class="input-group">
                                                  <span class="input-group-text">₱</span>
                                                  <input id="fld_price_{{ $product->id }}" type="number" class="form-control"
                                                    name="price" step="0.01" min="0.01" value="{{ $product->price }}" required>
                                                </div>
                                                <div class="invalid-feedback">Enter a valid price.</div>
                                              </div>
                                              <div class="col-md-4">
                                                <label class="form-label fw-semibold">Unit <span
                                                    class="text-danger">*</span></label>
                                                <select id="fld_unit_{{ $product->id }}" class="form-select" name="unit"
                                                  required>
                                                  <option value="" disabled>Select unit</option>
                                                  <option value="kg" {{ ($product->unit ?? '') === 'kg' ? 'selected' : '' }}>
                                                    Kilogram (kg)</option>
                                                  <option value="piece" {{ ($product->unit ?? '') === 'piece' ? 'selected' : '' }}>Piece</option>
                                                  <option value="bundle" {{ ($product->unit ?? '') === 'bundle' ? 'selected' : '' }}>Bundle</option>
                                                  <option value="sack" {{ ($product->unit ?? '') === 'sack' ? 'selected' : '' }}>
                                                    Sack</option>
                                                </select>
                                                <div class="invalid-feedback">Please choose a unit.</div>
                                              </div>
                                              <div class="col-md-4">
                                                <label class="form-label fw-semibold">Stock <span
                                                    class="text-danger">*</span></label>
                                                <input id="fld_stock_{{ $product->id }}" type="number" class="form-control"
                                                  name="stock" min="0" step="1" value="{{ $product->stock }}" required>
                                                <div class="invalid-feedback">Provide available stock.</div>
                                              </div>
                                            </div>

                                            <div class="row g-3">
                                              <div class="col-md-6 col-lg-5">
                                                <label class="form-label fw-semibold">Min order quantity</label>
                                                <input id="fld_moq_{{ $product->id }}" type="number" class="form-control"
                                                  name="min_order_qty" min="1" step="1" value="{{ $product->min_order_qty }}">
                                              </div>
                                              <div class="col-md-6 col-lg-5">
                                                <label class="form-label fw-semibold">Weight per unit <span
                                                    class="text-danger">*</span></label>
                                                <div class="input-group">
                                                  <input id="fld_weight_{{ $product->id }}" type="number" class="form-control"
                                                    name="weight" step="0.01" min="0.01" value="{{ $product->weight }}"
                                                    required>
                                                  <span class="input-group-text">kg</span>
                                                </div>
                                                <small class="text-muted">Example: 1 mango ≈ 0.25 kg</small>
                                                <div class="invalid-feedback">Enter weight per unit.</div>
                                              </div>
                                            </div>

                                            <div class="mt-2">
                                              <label class="form-label fw-semibold">Category</label>
                                              <select class="form-select" name="category_id" required>
                                                <option value="">Select category</option>
                                                @foreach($mainCategories as $category)
                                                  <optgroup label="{{ $category->name }}">
                                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                      {{ $category->name }} (Main)
                                                    </option>
                                                    @foreach ($category->subcategories as $subCategory)
                                                      <option value="{{ $subCategory->id }}" {{ $product->category_id == $subCategory->id ? 'selected' : '' }}>
                                                        └ {{ $subCategory->name }}
                                                      </option>
                                                    @endforeach
                                                  </optgroup>
                                                @endforeach
                                              </select>
                                            </div>
                                          </div>
                                        </div>
                                      </div> {{-- /row --}}
                                    </div> {{-- /modal-body --}}

                                    <div class="modal-footer border-0 pt-0">
                                      <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                      <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Product
                                      </button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                            {{-- Confirm Delete Modal --}}
                            <div class="modal fade" id="confirmDelete{{ $product->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title fw-semibold">Delete product</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body pt-0">
                                    <div class="d-flex align-items-start gap-2">
                                      <i class="fas fa-triangle-exclamation text-danger mt-1"></i>
                                      <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <div class="text-muted small">This action cannot be undone.</div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                      data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="ms-1">
                                      @csrf @method('DELETE')
                                      <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                @else
                  <p class="text-center text-muted">No products available.</p>
                @endif
              </div>
            </div>

            <!-- Add Product Modal-->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header border-0 pb-0">
                    <div>
                      <h5 class="modal-title fw-bold" id="addProductModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Add New Product
                      </h5>
                      <div class="text-muted small">Pick images once, preview on the left, edit details on the right.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <form class="needs-validation" novalidate method="POST" action="{{ route('products.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body pt-2">
                      <div class="row g-3">
                        <!-- LEFT: PREVIEW -->
                        <div class="col-lg-4">
                          <div class="border rounded-3 p-3 h-100">
                            <div class="fw-bold">Preview</div>
                            <div class="text-muted small mb-2">Product detail</div>

                            <!-- Swipeable carousel preview -->
                            <div id="previewCarousel" class="carousel slide mb-2" data-bs-touch="true">
                              <div class="carousel-inner ratio ratio-1x1 border rounded bg-light overflow-hidden"
                                id="carouselInner">
                                <div class="carousel-item active d-flex align-items-center justify-content-center">
                                  <span class="text-muted">No image yet</span>
                                </div>
                              </div>
                              <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                              </button>
                              <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                              </button>
                            </div>

                            <div class="mb-3">
                              <div class="form-control form-control-sm bg-light fw-semibold" readonly>
                                {{ auth()->user()->farm_name ?? 'Shop Name' }}
                              </div>
                            </div>

                            <div>
                              <div class="fw-bold mb-2">Details</div>
                              <div class="small">
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                  <span class="text-muted">Product:</span><span id="pv_name" class="fw-semibold">—</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                  <span class="text-muted">Price:</span><span id="pv_price" class="fw-semibold">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                  <span class="text-muted">Unit:</span><span id="pv_unit" class="fw-semibold">—</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                  <span class="text-muted">Stock:</span><span id="pv_stock" class="fw-semibold">0</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                  <span class="text-muted">Min order qty:</span><span id="pv_moq"
                                    class="fw-semibold">—</span>
                                </div>
                                <div class="d-flex justify-content-between py-1">
                                  <span class="text-muted">Weight / unit:</span><span id="pv_weight"
                                    class="fw-semibold">—</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- RIGHT: BASIC INFO -->
                        <div class="col-lg-8">
                          <div class="px-lg-1">
                            <div class="fw-bold">Basic Information</div>

                            {{-- Shopee-style Image Uploader (ADD) --}}
                            <div class="d-flex align-items-center justify-content-between mb-1">
                              <div class="fw-semibold">Product images</div>
                              <div class="small text-muted"><span id="shpAddCount">0</span>/9</div>
                            </div>

                            <div id="shpAddGrid" class="shp-grid">
                              {{-- Add tile --}}
                              <label class="shp-add">
                                <input id="shpAddPicker" type="file" accept="image/*" multiple hidden>
                                <div class="shp-add-inner">
                                  <div class="shp-plus">+</div>
                                  <div class="small text-muted">Add</div>
                                </div>
                              </label>
                            </div>

                            {{-- Hidden files mapped to your backend --}}
                            <input id="shpAddMainFile" name="image" type="file" class="d-none" required>
                            <input id="shpAddGalleryFiles" name="gallery[]" type="file" class="d-none" multiple>
                            <div class="form-text">Up to 9 images. Drag to reorder. First image is the Cover.</div>
                            <div class="invalid-feedback d-block" id="shpAddValidation" style="display:none;">Please select
                              at least one image.</div>


                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input id="fld_name" type="text" class="form-control" name="name" maxlength="120" required>
                            <div class="invalid-feedback">Product name is required.</div>

                            <label class="form-label fw-semibold mt-3">Description <span
                                class="text-danger">*</span></label>
                            <textarea id="fld_desc" class="form-control" name="description" rows="4" maxlength="800"
                              required></textarea>
                            <div class="invalid-feedback">Please add a short description.</div>

                            <div class="row g-3 mt-1">
                              <div class="col-md-4">
                                <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                  <span class="input-group-text">₱</span>
                                  <input id="fld_price" type="number" class="form-control" name="price" step="0.01"
                                    min="0.01" required>
                                </div>
                                <div class="invalid-feedback">Enter a valid price.</div>
                              </div>
                              <div class="col-md-4">
                                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                <select id="fld_unit" class="form-select" name="unit" required>
                                  <option value="" disabled selected>Select unit</option>
                                  <option value="kg">Kilogram (kg)</option>
                                  <option value="piece">Piece</option>
                                  <option value="bundle">Bundle</option>
                                  <option value="sack">Sack</option>
                                </select>
                                <div class="invalid-feedback">Please choose a unit.</div>
                              </div>
                              <div class="col-md-4">
                                <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                                <input id="fld_stock" type="number" class="form-control" name="stock" min="0" step="1"
                                  required>
                                <div class="invalid-feedback">Provide available stock.</div>
                              </div>
                            </div>

                            <div class="row g-3">
                              <div class="col-md-6 col-lg-5">
                                <label class="form-label fw-semibold">Min order quantity</label>
                                <input id="fld_moq" type="number" class="form-control" name="min_order_qty" min="1" step="1"
                                  placeholder="Optional">
                              </div>
                              <div class="col-md-6 col-lg-5">
                                <label class="form-label fw-semibold">Weight per unit <span
                                    class="text-danger">*</span></label>
                                <div class="input-group">
                                  <input id="fld_weight" type="number" class="form-control" name="weight" step="0.01"
                                    min="0.01" required>
                                  <span class="input-group-text">kg</span>
                                </div>
                                <small class="text-muted">Example: 1 mango ≈ 0.25 kg</small>
                                <div class="invalid-feedback">Enter weight per unit.</div>
                              </div>
                            </div>

                            <div class="mt-2">
                              <label class="form-label fw-semibold">Category</label>
                              <select class="form-select" name="category" required>
                                <option value="">Select category</option>
                                @foreach($mainCategories as $category)
                                  <optgroup label="{{ $category->name }}">
                                    <option value="{{ $category->id }}">
                                      {{ $category->name }} (Main)
                                    </option>
                                    @foreach ($category->subcategories as $subCategory)
                                      <option value="{{ $subCategory->id }}">
                                        └ {{ $subCategory->name }}
                                      </option>
                                    @endforeach
                                  </optgroup>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>
                      </div> <!-- /row -->
                    </div> <!-- /modal-body -->

                    <div class="modal-footer border-0 pt-0">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Save Product
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <style>
              /* subtle polish + make sure body is comfortably scrollable */
              #previewCarousel .carousel-inner img {
                object-fit: cover;
                width: 100%;
                height: 100%;
              }

              .modal-dialog-scrollable .modal-body {
                max-height: 68vh;
              }
            </style>

            <script>
              document.addEventListener('DOMContentLoaded', function () {
                // Bootstrap validation
                document.querySelectorAll('.needs-validation').forEach(function (form) {
                  form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
                    form.classList.add('was-validated');
                  }, false);
                });

                // Elements
                const picker = document.getElementById('imagePicker');
                const mainHidden = document.getElementById('imageMainHidden');
                const galleryHidden = document.getElementById('imageGalleryHidden');
                const galleryCount = document.getElementById('galleryCount');
                const inner = document.getElementById('carouselInner');

                // Build carousel slides from selected files
                function buildCarousel(files) {
                  inner.innerHTML = ''; // clear
                  if (!files || files.length === 0) {
                    inner.innerHTML = '<div class="carousel-item active d-flex align-items-center justify-content-center"><span class="text-muted">No image yet</span></div>';
                    return;
                  }
                  Array.from(files).forEach((file, idx) => {
                    const url = URL.createObjectURL(file);
                    const item = document.createElement('div');
                    item.className = 'carousel-item' + (idx === 0 ? ' active' : '');
                    item.innerHTML = `<img src="${url}" class="d-block w-100 h-100" alt="Preview ${idx + 1}">`;
                    inner.appendChild(item);
                  });
                }

                // Map single picker -> hidden main + gallery[] (first is main)
                function mapFilesToHiddenInputs(files) {
                  const list = Array.from(files || []);
                  const limited = list.slice(0, 9); // cap at 9
                  // counter
                  galleryCount.textContent = `(${limited.length}/9)`;

                  // main
                  const mainDT = new DataTransfer();
                  if (limited[0]) mainDT.items.add(limited[0]);
                  mainHidden.files = mainDT.files;

                  // gallery = remaining files
                  const galDT = new DataTransfer();
                  limited.slice(1).forEach(f => galDT.items.add(f));
                  galleryHidden.files = galDT.files;
                }

                if (picker) {
                  picker.addEventListener('change', function () {
                    const files = this.files || [];
                    if (files.length > 9) {
                      alert('Please select up to 9 images.');
                      // keep first 9 visually + in hidden inputs
                      const keep = Array.from(files).slice(0, 9);
                      buildCarousel(keep);
                      mapFilesToHiddenInputs(keep);
                      return;
                    }
                    buildCarousel(files);
                    mapFilesToHiddenInputs(files);
                  });
                }

                // Live preview of basic fields
                const money = (v) => {
                  const n = parseFloat(v);
                  return isNaN(n) ? '₱0.00' : '₱' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };
                const bind = (id, targetId, parser = (v) => v || '—') => {
                  const el = document.getElementById(id);
                  const tgt = document.getElementById(targetId);
                  if (!el || !tgt) return;
                  const update = () => tgt.textContent = parser(el.value);
                  el.addEventListener('input', update); update();
                };
                bind('fld_name', 'pv_name');
                bind('fld_price', 'pv_price', money);
                bind('fld_unit', 'pv_unit', v => v || '—');
                bind('fld_stock', 'pv_stock', v => (v === '' ? '0' : v));
                bind('fld_moq', 'pv_moq', v => v || '—');
                bind('fld_weight', 'pv_weight', v => v ? (parseFloat(v).toFixed(2) + ' kg') : '—');
              });
            </script>

            {{-- ========== Analytics (UNCHANGED LOGIC) ========== --}}
            <div class="tab-pane fade" id="analytics">
              <h4 class="fw-bold mb-3">Analytics Dashboard</h4>
              <div class="row">
                <div class="col-md-3">
                  <div class="card shadow-sm border-0 p-3 bg-success text-white">
                    <h6>Total Sales (Completed)</h6>
                    <h4>₱{{ number_format($completedSales, 2) }}</h4>
                    <h6>Pending Sales</h6>
                    <h4>₱{{ number_format($pendingSales, 2) }}</h4>
                    <h6>All Sales (Completed + Pending)</h6>
                    <h4>₱{{ number_format($totalSales, 2) }}</h4>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card shadow-sm border-0 p-3 bg-primary text-white">
                    <h6>Total Orders</h6>
                    <h4>{{ $totalOrders }}</h4>
                    <small>Completed: {{ $completedOrders }} | Pending: {{ $pendingOrders }}</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card shadow-sm border-0 p-3 bg-warning text-dark">
                    <h6>Top Selling Products</h6>
                    <h4>
                      @if($mostSoldProduct)
                        🏆 {{ $mostSoldProduct['product']->name }}
                        <small class="text-muted">({{ $mostSoldProduct['total_quantity'] }} sold)</small>
                      @else
                        No sales yet
                      @endif
                    </h4>
                    <ul class="list-group list-group-flush mt-3">
                      @forelse($topProducts as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          {{ $p['product']->name }}
                          <span class="badge bg-success rounded-pill">{{ $p['total_quantity'] }}</span>
                        </li>
                      @empty
                        <li class="list-group-item">No products sold yet</li>
                      @endforelse
                    </ul>
                  </div>
                </div>

                @if($lowStockProducts->count() > 0)
                  <div class="mt-3">
                    <h6>⚠️ Low Stock Products</h6>
                    <ul class="list-group">
                      @foreach($lowStockProducts as $prod)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          {{ $prod->name }}
                          <span class="badge bg-danger rounded-pill">{{ $prod->stock }} left</span>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @endif
              </div>

              <div class="mt-4">
                <h5 class="fw-bold">Revenue Trends</h5>
                <select id="filterType" class="form-select w-auto mb-3">
                  <option value="daily">Daily</option>
                  <option value="weekly">Weekly</option>
                  <option value="monthly" selected>Monthly</option>
                  <option value="quarterly">Quarterly</option>
                  <option value="yearly">Yearly</option>
                </select>
                <canvas id="salesChart"></canvas>
              </div>
            </div>
          </div>

        @else
        <p class="text-danger">You do not have permission to access this page.</p>
      @endif
    </div>
  </div>
  </div>

  <style>
    /* keep both cards same height */
    .row.g-3.mb-3 {
      align-items: stretch;
    }

    .row.g-3.mb-3>[class*="col-"] {
      display: flex;
    }

    .row.g-3.mb-3>[class*="col-"]>.bg-white {
      flex: 1 1 auto;
    }

    /* centered, balanced dropdown button */
    .action-menu-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      /* center icon + label + caret */
      gap: .625rem;
      min-width: 240px;
      /* nice readable width */
      height: 44px;
      padding: .5rem .75rem;
      border: 1px solid rgba(0, 0, 0, .2);
      background: #fff;
      color: #212529;
      border-radius: .5rem;
      font-weight: 600;
    }

    .action-menu-btn:hover {
      border-color: rgba(0, 0, 0, .28);
      box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .06);
    }

    /* menu same width as button and aligned under it */
    .dropdown .dropdown-menu {
      min-width: 100%;
      border-radius: .5rem;
      margin-top: .5rem;
    }

    .dropdown-item {
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    .dropdown-item.active {
      font-weight: 600;
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const labelEl = document.querySelector('#actionMenuBtn .action-label');
      const items = document.querySelectorAll('.dropdown-menu .dropdown-item.nav-link');

      function setActiveLabelFromId(id) {
        const activeItem = Array.from(items).find(a => a.dataset.target === id);
        if (activeItem && labelEl) labelEl.textContent = activeItem.textContent.trim();
        items.forEach(a => a.classList.toggle('active', a.dataset.target === id));
      }

      // Close dropdown after selecting and update label
      items.forEach(a => {
        a.addEventListener('click', function () {
          const dd = bootstrap.Dropdown.getOrCreateInstance(document.getElementById('actionMenuBtn'));
          dd.hide();
          setActiveLabelFromId(this.dataset.target);
        });
      });

      // Sync when hash/tab changes from elsewhere
      function syncFromHash() {
        const id = (location.hash || '#order-status').slice(1);
        setActiveLabelFromId(id);
      }
      window.addEventListener('hashchange', syncFromHash);
      syncFromHash();
    });
  </script>

  {{-- ====== Scripts ====== --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const ctx = document.getElementById('salesChart').getContext('2d');
      const filterSelect = document.getElementById('filterType');
      let chart;

      // Laravel data → JS (default monthly)
      const revenueTrends = @json($revenueTrends);

      const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ];
      const defaultLabels = Object.keys(revenueTrends).map(m => monthNames[m - 1]);
      const defaultData = Object.values(revenueTrends);

      // Draw chart
      function drawChart(labels, data) {
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Revenue (₱)',
              data: data,
              borderColor: 'rgba(75, 192, 192, 1)',
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              fill: true,
              tension: 0.3
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
          }
        });
      }

      // Fetch new data (for daily, weekly, etc.)
      function fetchRevenueData(type = 'monthly') {
        fetch(`/seller/revenue-data?type=${type}`)
          .then(res => res.json())
          .then(data => {
            const labels = Object.keys(data);
            const values = Object.values(data);
            drawChart(labels, values);
          })
          .catch(err => console.error('Fetch revenue data error:', err));
      }

      // Change filter
      filterSelect.addEventListener('change', function () {
        fetchRevenueData(this.value);
      });

      // Initial render (monthly)
      drawChart(defaultLabels, defaultData);
    });
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>

  {{-- Navigation (unchanged behaviour) --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const navLinks = document.querySelectorAll(".nav-link");
      const tabPanes = document.querySelectorAll(".tab-pane");
      navLinks.forEach(link => {
        link.addEventListener("click", function (e) {
          e.preventDefault();
          navLinks.forEach(nav => nav.classList.remove("active"));
          tabPanes.forEach(tab => tab.classList.remove("show", "active"));
          this.classList.add("active");
          const target = document.getElementById(this.getAttribute("data-target"));
          target.classList.add("show", "active");
        });
      });
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const addProductLinks = document.querySelectorAll('a[data-target="add-product"]');
      addProductLinks.forEach(link => {
        link.addEventListener("click", function (e) {
          e.preventDefault();

          // Show the My Shop tab
          const tabPanes = document.querySelectorAll(".tab-pane");
          tabPanes.forEach(tab => tab.classList.remove("show", "active"));
          const myShop = document.getElementById("my-shop");
          if (myShop) myShop.classList.add("show", "active");

          // Open the Add Product modal
          const modalEl = document.getElementById("addProductModal");
          if (modalEl) new bootstrap.Modal(modalEl).show();
        });
      });
    });
  </script>

  <style>
    .shp-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: .5rem;
    }

    @media (min-width: 576px) {
      .shp-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (min-width: 768px) {
      .shp-grid {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    .shp-item,
    .shp-add {
      position: relative;
      width: 100%;
      aspect-ratio: 1/1;
      border: 1px dashed rgba(0, 0, 0, .15);
      border-radius: .5rem;
      background: #f8f9fa;
      overflow: hidden;
      transition: box-shadow .12s ease, transform .12s ease, border-color .12s ease;
    }

    .shp-item:hover,
    .shp-add:hover {
      box-shadow: 0 .35rem .8rem rgba(0, 0, 0, .06);
      transform: translateY(-1px);
    }

    .shp-item.dragging {
      opacity: .6;
      border-color: #0d6efd;
    }

    .shp-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .shp-remove {
      position: absolute;
      top: .25rem;
      right: .25rem;
      border: 0;
      background: rgba(0, 0, 0, .55);
      color: #fff;
      width: 24px;
      height: 24px;
      line-height: 24px;
      border-radius: 50%;
      font-weight: 700;
      cursor: pointer;
    }

    .shp-cover {
      position: absolute;
      left: .25rem;
      bottom: .25rem;
      background: #0d6efd;
      color: #fff;
      font-size: .7rem;
      border-radius: .35rem;
      padding: .15rem .4rem;
    }

    .shp-add {
      display: grid;
      place-items: center;
      cursor: pointer;
    }

    .shp-add-inner {
      text-align: center;
      color: #6c757d;
    }

    .shp-plus {
      font-size: 1.75rem;
      line-height: 1;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.edit-product-modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
          if (modal.dataset.shopeeInit) return;
          modal.dataset.shopeeInit = '1';

          const pid = modal.getAttribute('data-pid');
          const grid = modal.querySelector('#shpGrid' + pid);
          const picker = modal.querySelector('#shpPicker' + pid);
          const count = modal.querySelector('#shpCount' + pid);
          const wrap = modal.querySelector('#shpExistingWrap' + pid);
          const mainH = modal.querySelector('#shpMainFile' + pid);
          const galH = modal.querySelector('#shpGalleryFiles' + pid);
          const inner = modal.querySelector('#editCarouselInner' + pid);

          // ---- state: { type:'existing'|'file', id|null, src, file? }
          let state = [];
          grid.querySelectorAll('.shp-item').forEach(tile => {
            const type = tile.dataset.type;
            const id = tile.dataset.existingId || null; // null => legacy main cover
            const src = tile.querySelector('img')?.src || '';
            state.push({ type: 'existing', id, src });
          });

          function syncCount() {
            if (count) count.textContent = String(state.length);
            grid.querySelectorAll('.shp-cover').forEach(b => b.remove());
            const first = grid.querySelector('.shp-item');
            if (first) {
              const tag = document.createElement('span');
              tag.className = 'shp-cover';
              tag.textContent = 'Cover';
              first.appendChild(tag);
            }
          }

          function rebuildCarousel() {
            if (!inner) return;
            inner.innerHTML = '';
            if (!state.length) {
              inner.innerHTML = `<div class="carousel-item active d-flex align-items-center justify-content-center">
            <span class="text-muted">No image yet</span>
          </div>`;
              return;
            }
            state.forEach((it, i) => {
              const slide = document.createElement('div');
              slide.className = 'carousel-item' + (i === 0 ? ' active' : '');
              slide.innerHTML = `<img src="${it.src}" class="d-block w-100 h-100" style="object-fit:cover;">`;
              inner.appendChild(slide);
            });
          }

          function makeFileTile(file) {
            const url = URL.createObjectURL(file);
            const tile = document.createElement('div');
            tile.className = 'shp-item'; tile.dataset.type = 'file'; tile.draggable = true;
            tile.innerHTML = `<img src="${url}"><button type="button" class="shp-remove" aria-label="Remove">&times;</button>`;
            return { tile, obj: { type: 'file', file, src: url, id: null } };
          }

          function rebindRemove(tile) {
            tile.querySelector('.shp-remove')?.addEventListener('click', () => {
              const tiles = Array.from(grid.querySelectorAll('.shp-item'));
              const idx = tiles.indexOf(tile);
              if (idx > -1) { state.splice(idx, 1); tile.remove(); syncCount(); rebuildCarousel(); }
            });
          }

          // Drag-sort
          grid.addEventListener('dragover', e => {
            e.preventDefault();
            const dragging = grid.querySelector('.dragging');
            if (!dragging) return;
            const tiles = [...grid.querySelectorAll('.shp-item:not(.dragging)')];
            const after = tiles.find(t => e.clientY < t.getBoundingClientRect().top + t.getBoundingClientRect().height / 2);
            if (after) grid.insertBefore(dragging, after); else grid.appendChild(dragging);
          });

          grid.addEventListener('drop', () => {
            const map = new Map(state.map(s => [s.src, s]));
            const next = [];
            grid.querySelectorAll('.shp-item img').forEach(img => {
              const s = map.get(img.src); if (s) next.push(s);
            });
            state = next; syncCount(); rebuildCarousel();
          });

          grid.querySelectorAll('.shp-item').forEach(tile => {
            tile.addEventListener('dragstart', e => { tile.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
            tile.addEventListener('dragend', () => tile.classList.remove('dragging'));
            rebindRemove(tile);
          });

          // Add files
          picker?.addEventListener('change', () => {
            const files = Array.from(picker.files || []);
            if (!files.length) return;
            const cap = 9 - state.length;
            files.slice(0, Math.max(0, cap)).forEach(f => {
              const { tile, obj } = makeFileTile(f);
              grid.insertBefore(tile, grid.querySelector('.shp-add'));
              tile.addEventListener('dragstart', e => { tile.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
              tile.addEventListener('dragend', () => tile.classList.remove('dragging'));
              rebindRemove(tile);
              state.push(obj);
            });
            picker.value = '';
            syncCount(); rebuildCarousel();
          });

          // Submit -> write hidden fields for controller
          modal.querySelector('form')?.addEventListener('submit', () => {
            wrap.innerHTML = '';

            // existing in current state (order)
            const existing = state.filter(s => s.type === 'existing');
            const orderIds = existing.map(s => (s.id ? s.id : 'main')); // 'main' (legacy) is ignored by controller reorder

            // figure out which initial existing tiles were removed
            const initialSrcs = new Set(
              Array.from(grid.querySelectorAll('.shp-item[data-type="existing"] img')).map(i => i.src)
            );
            const currentExistingSrcs = new Set(existing.map(s => s.src));

            // cover removed?
            const hadCover = Array.from(grid.querySelectorAll('.shp-item[data-type="existing"]'))[0];
            const coverSrc = hadCover ? (hadCover.querySelector('img')?.src || '') : '';
            const coverStillThere = existing.length && existing[0].src === coverSrc;
            if (coverSrc && !coverStillThere) {
              const rc = document.createElement('input');
              rc.type = 'hidden'; rc.name = 'remove_cover'; rc.value = '1';
              wrap.appendChild(rc);
            }

            // gallery removed: add id if we have it, otherwise URL (controller handles both)
            initialSrcs.forEach(src => {
              if (!currentExistingSrcs.has(src)) {
                // try find original tile to get its ID
                const origTile = Array.from(grid.querySelectorAll('.shp-item[data-type="existing"]'))
                  .find(t => t.querySelector('img')?.src === src);
                const id = origTile?.dataset.existingId || null;

                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'remove_existing[]'; inp.value = id ? String(id) : src;
                wrap.appendChild(inp);
              }
            });

            // order
            orderIds.forEach(val => {
              const inp = document.createElement('input');
              inp.type = 'hidden'; inp.name = 'existing_order[]'; inp.value = String(val);
              wrap.appendChild(inp);
            });

            // map new files: first => image, rest => gallery[]
            const files = state.filter(s => s.type === 'file').map(s => s.file);
            const mainDT = new DataTransfer();
            const galDT = new DataTransfer();
            if (files[0]) mainDT.items.add(files[0]);
            files.slice(1).forEach(f => galDT.items.add(f));
            mainH.files = mainDT.files;
            galH.files = galDT.files;
          });

          // initial paint
          syncCount(); rebuildCarousel();
        });
      });
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const addModalEl = document.getElementById('addProductModal');
      if (!addModalEl) return;

      // Elements (ADD)
      const grid = addModalEl.querySelector('#shpAddGrid');
      const picker = addModalEl.querySelector('#shpAddPicker');
      const countEl = addModalEl.querySelector('#shpAddCount');
      const errEl = addModalEl.querySelector('#shpAddValidation');
      const inner = addModalEl.querySelector('#carouselInner');
      const form = addModalEl.querySelector('form');
      const submitBtn = form.querySelector('button[type="submit"]');

      let filesState = []; // stores { file, src, tile }

      function updateCount() {
        if (countEl) countEl.textContent = String(filesState.length);
        if (errEl) errEl.style.display = filesState.length ? 'none' : 'block';
      }

      function markCoverInGrid() {
        grid.querySelectorAll('.shp-cover').forEach(n => n.remove());
        const firstTile = grid.querySelector('.shp-item');
        if (firstTile) {
          let tag = firstTile.querySelector('.shp-cover');
          if (!tag) {
            tag = document.createElement('span');
            tag.className = 'shp-cover';
            tag.textContent = 'Cover';
            firstTile.appendChild(tag);
          }
        }
      }

      function rebuildCarousel() {
        if (!inner) return;
        inner.innerHTML = '';
        if (filesState.length === 0) {
          inner.innerHTML = `<div class="carousel-item active d-flex align-items-center justify-content-center"><span class="text-muted">No image yet</span></div>`;
          return;
        }
        filesState.forEach((o, i) => {
          const slide = document.createElement('div');
          slide.className = 'carousel-item' + (i === 0 ? ' active' : '');
          slide.innerHTML = `<img src="${o.src}" class="d-block w-100 h-100" style="object-fit:cover;" alt="Preview ${i + 1}">`;
          inner.appendChild(slide);
        });
      }

      function makeTileForFile(file) {
        const url = URL.createObjectURL(file);
        const tile = document.createElement('div');
        tile.className = 'shp-item';
        tile.setAttribute('draggable', 'true');
        tile.dataset.type = 'new';
        tile.innerHTML = `<img src="${url}" alt=""><button type="button" class="shp-remove" aria-label="Remove">&times;</button>`;

        const removeBtn = tile.querySelector('.shp-remove');
        removeBtn.addEventListener('click', () => {
          const idx = filesState.findIndex(s => s.src === url);
          if (idx > -1) {
            // revoke object URL
            try { URL.revokeObjectURL(filesState[idx].src); } catch (e) { }
            filesState[idx].tile.remove();
            filesState.splice(idx, 1);
            updateCount();
            markCoverInGrid();
            rebuildCarousel();
          }
        });

        // Drag events
        tile.addEventListener('dragstart', e => {
          tile.classList.add('dragging');
          e.dataTransfer.effectAllowed = 'move';
        });
        tile.addEventListener('dragend', () => {
          tile.classList.remove('dragging');
          // Wait a moment to ensure DOM order has finalized
          setTimeout(() => {
            updateStateFromDOM();
            refreshCountAndCover();  // 🧩 ensure cover is reassigned
            rebuildCarousel();
            mapToHidden();
          }, 50);
        });

        return { tile, url, file };
      }

      function updateStateFromDOM() {
        const imgs = [...grid.querySelectorAll('.shp-item img')];
        const map = new Map(filesState.map(o => [o.src, o])); // src -> obj
        const newState = [];
        imgs.forEach(img => {
          const obj = map.get(img.src);
          if (obj) newState.push(obj);
        });
        filesState = newState;
        updateCount();
        markCoverInGrid();
        rebuildCarousel();
      }

      // drag-over sort
      grid.addEventListener('dragover', function (e) {
        e.preventDefault();
        const dragging = grid.querySelector('.dragging');
        if (!dragging) return;
        const tiles = [...grid.querySelectorAll('.shp-item:not(.dragging)')];
        const after = tiles.find(t => {
          const r = t.getBoundingClientRect();
          return e.clientY < r.top + r.height / 2;
        });
        if (after) grid.insertBefore(dragging, after);
        else grid.appendChild(dragging);
      });
      grid.addEventListener('drop', () => {
        updateStateFromDOM();
        refreshCountAndCover(); // reassign cover after dropping
        rebuildCarousel();
        mapToHidden();
      });

      // picker handler (add new files)
      picker.addEventListener('change', function () {
        const chosen = Array.from(picker.files || []);
        if (!chosen.length) return;
        const space = 9 - filesState.length;
        const toAdd = space > 0 ? chosen.slice(0, space) : [];
        toAdd.forEach(file => {
          const { tile, url, file: f } = makeTileForFile(file);
          grid.insertBefore(tile, grid.querySelector('.shp-add'));
          filesState.push({ file: f, src: url, tile });
        });
        picker.value = '';
        updateCount();
        markCoverInGrid();
        rebuildCarousel();
      });

      // Reset modal on open
      addModalEl.addEventListener('show.bs.modal', function () {
        // revoke old object URLs and cleanup new tiles (keep existing ones if any)
        filesState.forEach(s => {
          try { URL.revokeObjectURL(s.src); } catch (e) { }
          if (s.tile && s.tile.dataset.type !== 'existing') {
            s.tile.remove();
          }
        });
        filesState = [];
        if (errEl) errEl.style.display = 'none';
        updateCount();
        markCoverInGrid();
        rebuildCarousel();
      });

      // If modal closed without submit, also revoke URLs to free memory
      addModalEl.addEventListener('hidden.bs.modal', function () {
        filesState.forEach(s => {
          try { URL.revokeObjectURL(s.src); } catch (e) { }
        });
        filesState = [];
        if (errEl) errEl.style.display = 'none';
        updateCount();
        rebuildCarousel();
      });

      // FORM SUBMIT — use FormData directly and fetch
      if (form) {
        form.addEventListener('submit', async function (e) {
          e.preventDefault();

          if (filesState.length === 0) {
            if (errEl) errEl.style.display = 'block';
            return;
          }

          // disable submit button
          if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Uploading…';
          }

          const formData = new FormData(form); // includes _token from @csrf hidden input

          // append cover (first file) and gallery[]
          formData.set('image', filesState[0].file);
          // ensure gallery[] cleared then set others
          // Note: no need to remove existing keys when using set/append, but to be safe:
          // append remaining as gallery[]
          filesState.slice(1).forEach(o => {
            formData.append('gallery[]', o.file);
          });

          try {
            const response = await fetch(form.action, {
              method: 'POST',
              body: formData,
              credentials: 'same-origin' // include cookies (auth/session)
            });

            if (response.ok) {
              // success — reload to show the new product
              location.reload();
            } else if (response.status === 422) {
              // validation errors from Laravel
              const data = await response.json().catch(() => null);
              if (data && data.errors) {
                // compile error messages
                const msgs = Object.values(data.errors).flat().join("\n");
                alert('Validation error:\n' + msgs);
              } else {
                const txt = await response.text();
                alert('Validation failed:\n' + txt);
              }
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
              }
            } else {
              const txt = await response.text();
              console.error('Upload failed:', txt);
              alert('Failed to upload product. See console for details.');
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
              }
            }
          } catch (err) {
            console.error('Upload error:', err);
            alert('Upload error. Check console.');
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.innerHTML = originalText;
            }
          }
        });
      }

      // initial render
      updateCount();
      markCoverInGrid();
      rebuildCarousel();
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Find all edit modals
      document.querySelectorAll('[id^="editProductModal"]').forEach(modalEl => {
        const productId = modalEl.dataset.productId || modalEl.id.replace('editProductModal', '');
        const grid = modalEl.querySelector(`#shpGrid${productId}`);
        const picker = modalEl.querySelector(`#shpPicker${productId}`);
        const countEl = modalEl.querySelector(`#shpCount${productId}`);
        const mainH = modalEl.querySelector(`#shpMainFile${productId}`);
        const galH = modalEl.querySelector(`#shpGalleryFiles${productId}`);
        const existingWrap = modalEl.querySelector(`#shpExistingWrap${productId}`);
        const carouselInner = modalEl.querySelector(`#editCarouselInner${productId}`);
        if (!grid) return;

        let newFilesState = []; // { file, src, key }
        let initialized = false;

        // ✅ Function to manage hidden input for existing cover
        function updateCoverExistingInput() {
          if (!existingWrap) return;
          const firstTile = grid.querySelector('.shp-item:not(.shp-add)');
          if (!firstTile) return;

          // find or create hidden input
          let coverInput = modalEl.querySelector('input[name="cover_existing"]');
          if (!coverInput) {
            coverInput = document.createElement('input');
            coverInput.type = 'hidden';
            coverInput.name = 'cover_existing';
            modalEl.querySelector('form').appendChild(coverInput);
          }

          const img = firstTile.querySelector('img');
          if (!img) return;

          // only save path if image is from storage (not blob)
          if (firstTile.dataset.type === 'existing' && img.src.includes('/storage/')) {
            const path = img.src
              .replace(window.location.origin + '/storage/', '')
              .replace('http://127.0.0.1:8000/storage/', '')
              .replace('http://localhost/storage/', '')
              .replace('/storage/', '');
            coverInput.value = path;
            console.log(`[Edit:${productId}] cover_existing set ->`, path);
          } else {
            coverInput.value = ''; // if new upload or blob, handled separately
          }
        }

        // ✅ Update image count and reassign cover tag
        function refreshCountAndCover() {
          if (countEl)
            countEl.textContent = String(grid.querySelectorAll('.shp-item:not(.shp-add)').length);

          grid.querySelectorAll('.shp-cover').forEach(b => b.remove());

          const first = grid.querySelector('.shp-item:not(.shp-add)');
          if (first) {
            const tag = document.createElement('span');
            tag.className = 'shp-cover';
            tag.textContent = 'Cover';
            first.appendChild(tag);
          }

          // Only update cover input (mapToHidden handled separately)
          updateCoverExistingInput();
        }

        function rebuildCarousel() {
          if (!carouselInner) return;
          carouselInner.innerHTML = '';
          const imgs = grid.querySelectorAll('.shp-item img');
          if (!imgs.length) {
            carouselInner.innerHTML = `<div class="carousel-item active d-flex align-items-center justify-content-center"><span class="text-muted">No image yet</span></div>`;
            return;
          }
          imgs.forEach((img, i) => {
            const slide = document.createElement('div');
            slide.className = 'carousel-item' + (i === 0 ? ' active' : '');
            slide.innerHTML = `<img src="${img.src}" class="d-block w-100 h-100" style="object-fit:cover;">`;
            carouselInner.appendChild(slide);
          });
        }

        // ✅ Make tile for new uploaded file
        function makeTileForNewFile(file) {
          const url = URL.createObjectURL(file);
          const key = `${file.name}-${file.lastModified}`; // stable unique key
          const tile = document.createElement('div');
          tile.className = 'shp-item';
          tile.dataset.type = 'new';
          tile.innerHTML = `
        <img src="${url}" alt="" data-key="${key}">
        <button type="button" class="shp-remove" aria-label="Remove">&times;</button>
      `;
          tile.querySelector('.shp-remove').addEventListener('click', () => {
            const i = newFilesState.findIndex(s => s.key === key);
            if (i > -1) newFilesState.splice(i, 1);
            tile.remove();
            refreshCountAndCover();
            rebuildCarousel();
            mapToHidden();
          });
          tile.setAttribute('draggable', 'true');
          tile.addEventListener('dragstart', e => {
            tile.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
          });
          tile.addEventListener('dragend', () => {
            tile.classList.remove('dragging');
            updateStateFromDOM();
          });
          tile.addEventListener('dragover', e => e.preventDefault());
          return { tile, url, file, key };
        }

        // ✅ Keep state order in sync with DOM
        function updateStateFromDOM() {
          const mapByKey = new Map(newFilesState.map(o => [o.key, o]));
          const newState = [];
          grid.querySelectorAll('.shp-item img').forEach(img => {
            const key = img.dataset.key;
            const obj = mapByKey.get(key);
            if (obj) newState.push(obj);
          });
          newFilesState = newState;
          refreshCountAndCover();
          rebuildCarousel();
          mapToHidden();
          updateCoverExistingInput();
        }

        // ✅ Map new files to hidden inputs
        function mapToHidden() {
          if (mainH && galH) {
            const mainDT = new DataTransfer();
            const galDT = new DataTransfer();
            const hasExistingMain = !!grid.querySelector('.shp-item[data-type="existing"]:first-child');
            if (newFilesState.length > 0) {
              if (hasExistingMain) {
                newFilesState.forEach(o => galDT.items.add(o.file));
              } else {
                mainDT.items.add(newFilesState[0].file);
                newFilesState.slice(1).forEach(o => galDT.items.add(o.file));
              }
            }
            mainH.files = mainDT.files;
            galH.files = galDT.files;
            console.log(`[Edit:${productId}] mapped new files -> main: ${mainH.files.length}, gallery: ${galH.files.length}`);
          }
        }

        // ✅ Handle remove button click (existing & new)
        function onGridClick(e) {
          if (!e.target.classList.contains('shp-remove')) return;
          const tile = e.target.closest('.shp-item');
          if (!tile) return;

          const existingId = tile.dataset.existingId;
          const tileImg = tile.querySelector('img');
          const removeInput = modalEl.querySelector('.removeExistingInput');

          // existing image
          if (tile.dataset.type === 'existing' || existingId) {
            if (removeInput) {
              let arr;
              try { arr = JSON.parse(removeInput.value || '[]'); } catch { arr = []; }
              let val = '';
              if (tileImg && tileImg.src) {
                val = tileImg.src
                  .replace('http://127.0.0.1:8000/storage/', '')
                  .replace('http://localhost/storage/', '')
                  .replace('/storage/', '');
              } else if (existingId) val = existingId;
              if (val) {
                arr.push(val);
                removeInput.value = JSON.stringify(arr);
                console.log(`[Edit:${productId}] queued remove_existing =`, val);
              }
            }
            tile.remove();
            refreshCountAndCover();
            rebuildCarousel();
            updateCoverExistingInput();
            return;
          }

          // new tile
          if (tile.dataset.type === 'new') {
            const img = tile.querySelector('img');
            const key = img ? img.dataset.key : null;
            const i = newFilesState.findIndex(s => s.key === key);
            if (i > -1) newFilesState.splice(i, 1);
            tile.remove();
            refreshCountAndCover();
            rebuildCarousel();
            mapToHidden();
          }
        }

        // ✅ Initialize event listeners (only once)
        function initListenersOnce() {
          if (initialized) return;
          initialized = true;

          grid.addEventListener('click', onGridClick);

          // Drag sorting
          grid.addEventListener('dragover', function (e) {
            e.preventDefault();
            const dragging = grid.querySelector('.dragging');
            if (!dragging) return;
            const tiles = [...grid.querySelectorAll('.shp-item:not(.dragging)')];
            const after = tiles.find(t => {
              const r = t.getBoundingClientRect();
              return e.clientY < r.top + r.height / 2;
            });
            if (after) grid.insertBefore(dragging, after);
            else grid.appendChild(dragging);
          });
          grid.addEventListener('drop', updateStateFromDOM);

          // File picker
          if (picker) {
            picker.addEventListener('change', function () {
              const chosen = Array.from(picker.files || []);
              if (!chosen.length) return;
              const space = 9 - grid.querySelectorAll('.shp-item').length;
              const toAdd = space > 0 ? chosen.slice(0, space) : [];
              toAdd.forEach(file => {
                const { tile, url, file: f, key } = makeTileForNewFile(file);
                newFilesState.push({ file: f, src: url, key });
                const addTile = grid.querySelector('.shp-add');
                grid.insertBefore(tile, addTile);
              });
              picker.value = '';
              refreshCountAndCover();
              rebuildCarousel();
              mapToHidden();
            });
          }

          // AJAX form submission
          const form = modalEl.querySelector('form');
          if (form) {
            form.addEventListener('submit', async function (e) {
              e.preventDefault();
              mapToHidden();

              const formData = new FormData(form);
              const action = form.getAttribute('action');
              const method = form.getAttribute('method') || 'POST';

              try {
                const res = await fetch(action, {
                  method: method,
                  body: formData,
                  headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                const data = await res.json();
                if (data.success) {
                  console.log('[Edit] Update success:', data.message);
                  location.reload();
                } else {
                  alert('Update failed. Please try again.');
                }
              } catch (err) {
                console.error('Update error:', err);
                alert('An error occurred while updating the product.');
              }
            });
          }
        }

        // Modal show
        modalEl.addEventListener('show.bs.modal', function () {
          initListenersOnce();
          grid.querySelectorAll('.shp-item').forEach(tile => {
            if (tile.dataset.type !== 'existing' && !tile.classList.contains('shp-add')) tile.remove();
          });
          newFilesState = [];
          if (existingWrap) existingWrap.innerHTML = '';
          if (mainH) mainH.value = '';
          if (galH) galH.value = '';
          refreshCountAndCover();
          rebuildCarousel();
        });

        // Modal hide cleanup
        modalEl.addEventListener('hidden.bs.modal', function () {
          newFilesState.forEach(o => { try { URL.revokeObjectURL(o.src) } catch (e) { } });
          newFilesState = [];
          if (existingWrap) existingWrap.innerHTML = '';
          if (mainH) mainH.value = '';
          if (galH) galH.value = '';
          refreshCountAndCover();
          rebuildCarousel();
        });

        // Initial sync
        refreshCountAndCover();
        rebuildCarousel();
      });
    });
  </script>


  <script src="https://kit.fontawesome.com/YOUR_KIT_CODE.js" crossorigin="anonymous"></script>
</x-app-layout>