<x-app-layout>
  <div class="container mt-5">
    <h2 class="mb-4">Shopping Cart</h2>

    @if($cartItems->count() > 0)
      {{-- Shopee-like cart layout --}}
      <div class="row">
        <div class="col-12">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="form-check m-0">
              <input type="checkbox" class="form-check-input" id="select-all">
              <label class="form-check-label" for="select-all">Select All</label>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" id="delete-selected" disabled>Remove</button>
          </div>
        </div>

        <div class="col-12">
          @php
            // Group by seller for a Shopee-like layout
            $groups = $cartItems->filter(fn($i) => $i->product)->groupBy(fn($i) => $i->product->user_id);
          @endphp

          @foreach($groups as $sellerId => $items)
            @php
              $seller = optional($items->first()->product->user);
              $shopName = $seller->username ?? $seller->name ?? 'Seller';
            @endphp

            <div class="card mb-3 shop-block" data-seller-id="{{ $sellerId }}">
              <div class="card-header bg-white d-flex align-items-center gap-2">
                <input type="checkbox" class="form-check-input shop-checkbox" data-seller="{{ $sellerId }}">
                <div class="fw-semibold">{{ $shopName }}</div>
                <a href="{{ route('chat', $sellerId) }}" class="btn btn-sm btn-outline-success ms-auto">Chat</a>
              </div>

              <div class="list-group list-group-flush">
                @foreach($items as $cartItem)
                  @php
                    $p = $cartItem->product;
                    $price = (float) $p->price;
                    $subtotal = $price * $cartItem->quantity;
                    $img = $p->image ? asset('storage/'.$p->image) : asset('assets/products.jpg');
                  @endphp

                  <div class="list-group-item py-3" id="cart-item-{{ $cartItem->id }}"
                       data-id="{{ $cartItem->id }}" data-seller-id="{{ $p->user_id }}">
                    <div class="row align-items-center g-2">
                      <div class="col-auto">
                        <input type="checkbox"
                               name="selected_items[]"
                               value="{{ $cartItem->id }}"
                               class="form-check-input product-checkbox"
                               data-price="{{ $price }}"
                               data-product-id="{{ $cartItem->id }}"
                               data-product-name="{{ $p->name }}">
                      </div>

                      <div class="col-auto">
                        <img src="{{ $img }}" alt="{{ $p->name }}" class="cart-thumb rounded">
                      </div>

                      <div class="col">
                        <div class="cart-title text-truncate fw-semibold">{{ $p->name }}</div>
                        <div class="small text-muted">
                          ₱{{ number_format($price,2) }}
                          @if(!empty($p->unit))
                            <span class="text-muted">/ {{ $p->unit }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="col-auto">
                        <div class="input-group input-group-sm qty-group">
                          <button type="button" class="btn btn-outline-secondary decrement-btn" data-id="{{ $cartItem->id }}">−</button>
                          <input type="text" class="form-control text-center quantity-input"
                                 value="{{ $cartItem->quantity }}" data-id="{{ $cartItem->id }}" readonly>
                          <button type="button" class="btn btn-outline-secondary increment-btn" data-id="{{ $cartItem->id }}">+</button>
                        </div>
                      </div>

                      <div class="col-auto fw-semibold">
                        <span class="subtotal">₱{{ number_format($subtotal,2) }}</span>
                      </div>

                      <div class="col-auto">
                        <button type="button" class="btn btn-link text-danger remove-item p-0" data-id="{{ $cartItem->id }}">
                          Remove
                        </button>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="card-footer bg-white small text-muted d-flex align-items-center">
                <i class="fas fa-truck me-2"></i> Shipping: ₱50 per shop (auto-added in total)
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Sticky bottom bar like Shopee --}}
      <div class="cart-bottom-bar shadow-lg">
        <div class="container d-flex align-items-center flex-wrap gap-3 py-2">
          <div class="form-check m-0">
            <input type="checkbox" class="form-check-input" id="select-all-bottom">
            <label class="form-check-label" for="select-all-bottom">Select All</label>
          </div>

          <button type="button" class="btn btn-outline-danger btn-sm" id="delete-selected-bottom" disabled>Remove</button>

          <div class="ms-auto text-end">
            <div class="small text-muted">Shipping included (₱50 per shop)</div>
            <div class="h5 m-0">Total: ₱<span id="total-price">0.00</span></div>
          </div>

          <button class="btn btn-success ms-2 proceed-to-checkout" id="checkout-btn" disabled>
            Checkout (<span id="selected-count">0</span>)
          </button>
        </div>
      </div>

      {{-- Loading overlay (kept for UX) --}}
      <div id="loading-screen" class="loading-overlay d-none">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    @else
      <p class="text-muted">Your cart is empty. <a href="{{ route('shop') }}">Continue Shopping</a></p>
    @endif
  </div>

  {{-- Styles --}}
  <style>
    /* Shopee-like cart styles */
    .cart-thumb { width: 72px; height: 72px; object-fit: cover; }
    .cart-title { max-width: 280px; }
    .qty-group .btn { width: 36px; }
    .shop-block .list-group-item { border-left: 0; border-right: 0; }
    .shop-block .card-header { border-bottom: 1px solid rgba(0,0,0,.05); }

    /* Sticky bottom bar */
    .cart-bottom-bar {
      position: sticky; bottom: 0; background: #fff; z-index: 1030;
      border-top: 1px solid rgba(0,0,0,.1);
    }

    /* Loading overlay you already use elsewhere */
    .loading-overlay {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(255, 255, 255, 0.7);
      display: flex; justify-content: center; align-items: center;
      z-index: 9999;
    }

    /* Mobile tightening */
    @media (max-width: 576px) {
      .cart-title { max-width: 160px; }
      .cart-thumb { width: 60px; height: 60px; }
    }
  </style>

  {{-- Scripts --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const selectAllTop = document.getElementById("select-all");
      const selectAllBottom = document.getElementById("select-all-bottom");
      const deleteSelectedTop = document.getElementById("delete-selected");
      const deleteSelectedBottom = document.getElementById("delete-selected-bottom");
      const checkoutBtn = document.getElementById("checkout-btn");
      const loadingScreen = document.getElementById("loading-screen");

      const $ = (sel) => document.querySelector(sel);
      const $$ = (sel) => Array.from(document.querySelectorAll(sel));

      function showLoading(){ loadingScreen?.classList.remove("d-none"); }
      function hideLoading(){ loadingScreen?.classList.add("d-none"); }

      function setSelectAll(checked) {
        if (selectAllTop) selectAllTop.checked = checked;
        if (selectAllBottom) selectAllBottom.checked = checked;
      }

      function syncSelectAllState() {
        const cbs = $$(".product-checkbox");
        const allChecked = cbs.length && cbs.every(cb => cb.checked);
        setSelectAll(allChecked);
      }

      function updateTotal() {
        let subtotal = 0;
        let selectedCount = 0;
        const sellers = new Set();

        $$(".product-checkbox:checked").forEach(cb => {
          const row = cb.closest(".list-group-item");
          const price = parseFloat(cb.dataset.price || 0);
          const qty = parseInt(row.querySelector(".quantity-input").value || "1");
          subtotal += price * qty;
          selectedCount++;
          sellers.add(row.dataset.sellerId);
        });

        const shipping = selectedCount ? sellers.size * 50 : 0;
        const total = subtotal + shipping;

        $("#total-price").textContent = total.toFixed(2);
        $("#selected-count").textContent = selectedCount.toString();

        const hasChecked = selectedCount > 0;
        if (deleteSelectedTop) deleteSelectedTop.disabled = !hasChecked;
        if (deleteSelectedBottom) deleteSelectedBottom.disabled = !hasChecked;
        if (checkoutBtn) checkoutBtn.disabled = !hasChecked;

        // Sync shop cbs
        $$(".shop-block").forEach(block => {
          const sellerId = block.dataset.sellerId;
          const items = $$(`.list-group-item[data-seller-id="${sellerId}"] .product-checkbox`);
          const shopCb = block.querySelector(".shop-checkbox");
          if (shopCb) shopCb.checked = items.length && items.every(cb => cb.checked);
        });

        syncSelectAllState();
      }

      // MASTER SELECT (top & bottom)
      [selectAllTop, selectAllBottom].forEach(sel => {
        sel?.addEventListener("change", function () {
          $$(".product-checkbox").forEach(cb => cb.checked = this.checked);
          $$(".shop-checkbox").forEach(sb => sb.checked = this.checked);
          updateTotal();
        });
      });

      // Shop-level checkbox
      document.addEventListener("change", function (e) {
        if (e.target.classList.contains("shop-checkbox")) {
          const sellerId = e.target.dataset.seller;
          const items = $$(`.list-group-item[data-seller-id="${sellerId}"] .product-checkbox`);
          items.forEach(cb => cb.checked = e.target.checked);
          updateTotal();
        }
      });

      // Individual product checkbox
      document.addEventListener("change", function (e) {
        if (e.target.classList.contains("product-checkbox")) {
          updateTotal();
        }
      });

      // Remove selected
      function removeSelected() {
        const selectedIds = $$(".product-checkbox:checked").map(cb => cb.value);
        if (!selectedIds.length) return alert("Select at least one item to delete.");
        if (!confirm("Remove selected items?")) return;

        showLoading();
        fetch("{{ route('cart.bulkDelete') }}", {
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ selected_items: selectedIds })
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            selectedIds.forEach(id => document.getElementById(`cart-item-${id}`)?.remove());
            // Remove empty shops
            $$(".shop-block").forEach(block => {
              if (!block.querySelector(".list-group-item")) block.remove();
            });
            updateTotal();
            if (!document.querySelector(".list-group-item")) window.location.reload();
          } else {
            alert("Error deleting items.");
          }
        })
        .catch(console.error)
        .finally(hideLoading);
      }
      deleteSelectedTop?.addEventListener("click", removeSelected);
      deleteSelectedBottom?.addEventListener("click", removeSelected);

      // Remove single item
      document.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-item")) {
          const itemId = e.target.dataset.id;
          if (!confirm("Remove this item?")) return;
          showLoading();
          fetch(`/cart/remove/${itemId}`, {
            method: "DELETE",
            headers: {
              "X-CSRF-TOKEN": "{{ csrf_token() }}",
              "Content-Type": "application/json",
              "Accept": "application/json"
            }
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              document.getElementById(`cart-item-${itemId}`)?.remove();
              $$(".shop-block").forEach(block => {
                if (!block.querySelector(".list-group-item")) block.remove();
              });
              updateTotal();
              if (!document.querySelector(".list-group-item")) window.location.reload();
            } else {
              alert(data.message || "Error removing item.");
            }
          })
          .catch(err => {
            console.error(err);
            alert("Something went wrong. Please try again.");
          })
          .finally(hideLoading);
        }
      });

      // Quantity update (reuses your existing endpoint)
      function updateQty(itemId, newQty, inputEl) {
        showLoading();
        fetch(`{{ route('cart.update', '') }}/${itemId}`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({ quantity: newQty })
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            inputEl.value = data.new_quantity;
            const price = parseFloat(document.querySelector(`.product-checkbox[value='${itemId}']`).dataset.price);
            const row = document.getElementById(`cart-item-${itemId}`);
            row.querySelector(".subtotal").textContent = `₱${(price * data.new_quantity).toFixed(2)}`;
            updateTotal();
          } else {
            alert(data.message || "Error updating quantity.");
          }
        })
        .catch(err => {
          console.error(err);
          alert("Something went wrong.");
        })
        .finally(hideLoading);
      }

      document.addEventListener("click", function (e) {
        if (e.target.classList.contains("increment-btn") || e.target.classList.contains("decrement-btn")) {
          const itemId = e.target.dataset.id;
          const input = document.querySelector(`.quantity-input[data-id='${itemId}']`);
          const current = parseInt(input.value || "1");
          const next = e.target.classList.contains("increment-btn") ? current + 1 : Math.max(1, current - 1);
          updateQty(itemId, next, input);
        }
      });

      // Checkout – same behavior you already had (prepareCheckout → redirect)
      $("#checkout-btn")?.addEventListener("click", function () {
        const selectedItems = $$(".product-checkbox:checked").map(cb => cb.value);
        if (!selectedItems.length) return alert("Please select at least one product.");

        fetch('/checkout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ selected_items: selectedItems })
        })
        .then(r => r.json())
        .then(data => {
          if (data.redirect_url) window.location.href = data.redirect_url;
          else alert('Something went wrong. Please try again.');
        })
        .catch(err => {
          console.error(err);
          alert('An error occurred. Please try again.');
        });
      });

      // Initial compute
      updateTotal();
    });
  </script>
</x-app-layout>
