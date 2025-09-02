<x-app-layout>
    <div class="container my-5 checkout-page">
        <h2 class="mb-4 fw-semibold">Checkout</h2>

        @if($cartItems->count() > 0)
            @php
                // Initial totals (all checked by default)
                $subtotal = 0;
                $shops = [];
                foreach ($cartItems as $ci) {
                    $subtotal += ($ci->product->price * $ci->quantity);
                    $shops[$ci->product->user_id] = true;
                }
                $shipping = count($shops) * 50; // ₱50 per shop
                $grandTotal = $subtotal + $shipping;

                // Choose selected/default address
                $selectedAddressId = session('selected_address_id') ?? request()->input('address_id');
                $selectedAddress = $user->addresses->where('id', $selectedAddressId)->first()
                                   ?? $user->addresses->first();
            @endphp

            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf

                <div class="row g-4">
                    <!-- LEFT: Items -->
                    <div class="col-12 col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-semibold">Items</h5>
                            </div>

                            <div class="list-group list-group-flush">
                                @foreach($cartItems as $cartItem)
                                    @php
                                        $line = $cartItem->product->price * $cartItem->quantity;
                                        $sellerName = optional($cartItem->product->user)->name;
                                    @endphp
                                    <div class="list-group-item py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <input
                                                class="form-check-input mt-0 item-check"
                                                type="checkbox"
                                                name="selected_items[]"
                                                value="{{ $cartItem->id }}"
                                                checked
                                                data-price="{{ $cartItem->product->price }}"
                                                data-qty="{{ $cartItem->quantity }}"
                                                data-seller="{{ $cartItem->product->user_id }}"
                                            >
                                            <img
                                                src="{{ asset('storage/' . $cartItem->product->image) }}"
                                                alt="{{ $cartItem->product->name }}"
                                                class="rounded border"
                                                style="width:76px;height:76px;object-fit:cover;"
                                            >
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $cartItem->product->name }}</div>
                                                @if($sellerName)
                                                    <div class="text-muted small">by {{ $sellerName }}</div>
                                                @endif
                                                <div class="text-muted small">
                                                    ₱{{ number_format($cartItem->product->price, 2) }}
                                                    <span class="mx-1">·</span>
                                                    Qty: {{ $cartItem->quantity }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-semibold text-danger">
                                                    ₱<span class="line-total">{{ number_format($line, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="card-footer bg-white py-3 small text-muted">
                                <i class="bi bi-truck"></i>
                                Shipping: ₱50 per shop (auto-calculated)
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Address + Payment + Summary -->
                    <div class="col-12 col-lg-4">
                        <div class="position-sticky" style="top: 90px;">

                            <!-- Address -->
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                    <h5 class="mb-0 fw-semibold">Shipping Address</h5>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="edit-address-btn">
                                        Edit
                                    </button>
                                </div>

                                <div class="card-body">
                                    @if($user->addresses->count() > 0 && $selectedAddress)
                                        <div class="d-flex gap-3 align-items-start" id="displayed-address">
                                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <div class="flex-grow-1 small">
                                                <div class="fw-semibold">
                                                    {{ $selectedAddress->full_name }} — {{ $selectedAddress->mobile_number }}
                                                </div>
                                                <div class="text-muted">
                                                    {{ $selectedAddress->floor_unit_number }},
                                                    {{ $selectedAddress->barangay }},
                                                    {{ $selectedAddress->city }},
                                                    {{ $selectedAddress->province }}
                                                </div>
                                                @if($selectedAddress->notes)
                                                    <div class="text-muted fst-italic">{{ $selectedAddress->notes }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <input type="hidden" name="address_id" value="{{ $selectedAddress->id }}">
                                    @else
                                        <button type="button" class="btn btn-success w-100" id="add-address-btn">+ Add Address</button>
                                    @endif
                                </div>
                            </div>

                            <!-- Payment -->
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 fw-semibold">Payment Method</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pm_gcash" value="gcash" required>
                                        <label class="form-check-label" for="pm_gcash">GCash</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pm_cod" value="cod">
                                        <label class="form-check-label" for="pm_cod">Cash on Delivery</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 fw-semibold">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span>₱<span id="sum-subtotal">{{ number_format($subtotal, 2) }}</span></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Shipping (₱50 × <span id="sum-shops">{{ count($shops) }}</span>)</span>
                                        <span>₱<span id="sum-shipping">{{ number_format($shipping, 2) }}</span></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fs-5 fw-bold">
                                        <span>Total</span>
                                        <span class="text-success">₱<span id="sum-total">{{ number_format($grandTotal, 2) }}</span></span>
                                    </div>

                                    <button type="button" id="proceed-btn" class="btn btn-success w-100 mt-3">
                                        Place Order
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="mb-0">Your cart is empty. <a href="{{ route('shop') }}">Go back to shop</a></p>
                </div>
            </div>
        @endif
    </div>

    {{-- Add Address Modal --}}
    <div id="addressModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header mb-2">
                <h4 class="mb-0">Add New Shipping Address</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="addressId" value="">
                <div class="modal-text">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" placeholder="First Last">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Other Notes</label>
                        <input type="text" class="form-control" placeholder="Enter notes">
                    </div>
                </div>
                <div class="modal-address">
                    <div class="mb-3">
                        <label class="form-label">Floor/Unit Number</label>
                        <input type="text" class="form-control" placeholder="Enter floor/unit number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Province</label>
                        <input type="text" class="form-control" placeholder="Enter province">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" placeholder="Enter city">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barangay</label>
                        <input type="text" class="form-control" placeholder="Enter barangay">
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-outline-secondary" id="cancelAddressBtn">Cancel</button>
                <button class="btn btn-primary" id="saveAddressBtn">Save</button>
            </div>
        </div>
    </div>

    {{-- Edit / Pick Address Modal (right) --}}
    <div id="editAddressModal" class="modal right-modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Select / Edit Address</h4>
                <button class="btn btn-success btn-sm" id="openAddAddressModal">Add</button>
            </div>
            <div class="modal-body">
                @foreach($addresses as $address)
                    <div class="address-card">
                        <input
                            type="radio"
                            name="selected_address"
                            id="address-{{ $address->id }}"
                            value="{{ $address->id }}"
                            class="form-check-input me-2"
                            @if(session('selected_address_id') == $address->id) checked @endif
                        >
                        <label for="address-{{ $address->id }}" class="ms-1">
                            <strong>{{ $address->full_name }} - {{ $address->mobile_number }}</strong><br>
                            {{ $address->floor_unit_number }}, {{ $address->barangay }}, {{ $address->city }}, {{ $address->province }}<br>
                            {{ $address->notes }}
                        </label>

                        <button class="edit-address-btn btn btn-link p-0 small" type="button"
                                data-id="{{ $address->id }}"
                                data-full_name="{{ $address->full_name }}"
                                data-mobile_number="{{ $address->mobile_number }}"
                                data-notes="{{ $address->notes }}"
                                data-floor_unit_number="{{ $address->floor_unit_number }}"
                                data-province="{{ $address->province }}"
                                data-city="{{ $address->city }}"
                                data-barangay="{{ $address->barangay }}">
                            Edit
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="modal-actions">
                <button class="btn btn-outline-secondary" id="closeEditAddressBtn">Close</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---------- DOM refs
            const addressModal = document.getElementById('addressModal');
            const editAddressModal = document.getElementById('editAddressModal');
            const addAddressBtn = document.getElementById('add-address-btn');
            const editAddressBtn = document.getElementById('edit-address-btn');
            const openAddAddressModal = document.getElementById('openAddAddressModal');
            const closeEditAddressBtn = document.getElementById('closeEditAddressBtn');
            const cancelAddressBtn = document.getElementById('cancelAddressBtn');
            const saveAddressBtn = document.getElementById('saveAddressBtn');
            const addressIdInput = document.getElementById('addressId');
            const proceedBtn = document.getElementById('proceed-btn');

            // ---------- Totals
            const itemChecks = document.querySelectorAll('.item-check');
            const sumSubtotal = document.getElementById('sum-subtotal');
            const sumShipping = document.getElementById('sum-shipping');
            const sumShops = document.getElementById('sum-shops');
            const sumTotal = document.getElementById('sum-total');

            function recalcSummary() {
                let subtotal = 0;
                const shopSet = new Set();
                itemChecks.forEach(cb => {
                    if (!cb.checked) return;
                    const price = parseFloat(cb.dataset.price || '0');
                    const qty   = parseInt(cb.dataset.qty || '1', 10);
                    const seller = cb.dataset.seller;
                    subtotal += price * qty;
                    shopSet.add(seller);
                });
                const shipping = shopSet.size * 50;
                const total = subtotal + shipping;

                sumSubtotal.textContent = subtotal.toFixed(2);
                sumShops.textContent = shopSet.size;
                sumShipping.textContent = shipping.toFixed(2);
                sumTotal.textContent = total.toFixed(2);
            }

            itemChecks.forEach(cb => cb.addEventListener('change', recalcSummary));
            recalcSummary();

            // ---------- Edit Address Modal
            editAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                editAddressModal.style.display = 'flex';
            });

            // Add Address
            addAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                clearAddressForm();
                addressIdInput.value = '';
                addressModal.style.display = 'flex';
                editAddressModal.style.display = 'none';
            });

            // Add from edit
            openAddAddressModal?.addEventListener('click', (e) => {
                e.preventDefault();
                clearAddressForm();
                addressIdInput.value = '';
                editAddressModal.style.display = 'none';
                addressModal.style.display = 'flex';
            });

            // Close modals
            closeEditAddressBtn?.addEventListener('click', () => editAddressModal.style.display = 'none');
            cancelAddressBtn?.addEventListener('click', () => addressModal.style.display = 'none');

            // Edit one address -> open add form with prefill
            document.querySelectorAll('.edit-address-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const d = this.dataset;
                    document.querySelector('input[placeholder="First Last"]').value = d.full_name || '';
                    document.querySelector('input[placeholder="Enter phone number"]').value = d.mobile_number || '';
                    document.querySelector('input[placeholder="Enter notes"]').value = d.notes || '';
                    document.querySelector('input[placeholder="Enter floor/unit number"]').value = d.floor_unit_number || '';
                    document.querySelector('input[placeholder="Enter province"]').value = d.province || '';
                    document.querySelector('input[placeholder="Enter city"]').value = d.city || '';
                    document.querySelector('input[placeholder="Enter barangay"]').value = d.barangay || '';
                    addressIdInput.value = d.id || '';

                    addressModal.style.display = 'flex';
                    editAddressModal.style.display = 'none';
                });
            });

            // Choose address -> updates hidden address_id + card
            document.querySelectorAll('input[name="selected_address"]').forEach(r => {
                r.addEventListener('change', function () {
                    const card = this.closest('.address-card');
                    const lbl  = card.querySelector('label');

                    // Simple parse of the label to show on right card
                    const lines = lbl.innerHTML.split('<br>');
                    const header = lines[0] ?? '';
                    const address1 = lines[1] ?? '';
                    const notes    = lines[2] ?? '';

                    document.querySelector('input[name="address_id"]').value = this.value;

                    const display = document.getElementById('displayed-address');
                    display.innerHTML = `
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="flex-grow-1 small">
                            <div class="fw-semibold">${header}</div>
                            <div class="text-muted">${address1}</div>
                            <div class="text-muted fst-italic">${notes}</div>
                        </div>
                    `;
                });
            });

            // Save (create/update) address
            saveAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();

                const payload = {
                    full_name: document.querySelector('input[placeholder="First Last"]').value,
                    mobile_number: document.querySelector('input[placeholder="Enter phone number"]').value,
                    notes: document.querySelector('input[placeholder="Enter notes"]').value,
                    floor_unit_number: document.querySelector('input[placeholder="Enter floor/unit number"]').value,
                    province: document.querySelector('input[placeholder="Enter province"]').value,
                    city: document.querySelector('input[placeholder="Enter city"]').value,
                    barangay: document.querySelector('input[placeholder="Enter barangay"]').value,
                };

                const id = addressIdInput.value;
                let url = '{{ route("checkout.saveAddress") }}';
                let method = 'POST';

                if (id) {
                    url = `/checkout/updateAddress/${id}`;
                    method = 'PUT';
                }

                fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        alert('Address saved successfully!');
                        addressModal.style.display = 'none';
                        location.reload();
                    } else {
                        alert(res.error || 'Something went wrong.');
                    }
                })
                .catch(err => console.error(err));
            });

            // Place Order
            proceedBtn?.addEventListener('click', function (e) {
                e.preventDefault();

                const addressId = document.querySelector('input[name="address_id"]')?.value;
                if (!addressId) return alert('Please select a shipping address.');

                const pm = document.querySelector('input[name="payment_method"]:checked');
                if (!pm) return alert('Please select a payment method.');

                const selectedItems = Array.from(document.querySelectorAll('input[name="selected_items[]"]:checked')).map(i => i.value);
                if (!selectedItems.length) return alert('Please select at least one product.');

                fetch("{{ route('checkout.process') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        address_id: addressId,
                        selected_items: selectedItems,
                        payment_method: pm.value
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.error || 'Something went wrong.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Something went wrong.');
                });
            });

            // Helpers
            function clearAddressForm() {
                document.querySelectorAll('#addressModal input').forEach(i => i.value = '');
            }
        });
    </script>

    <style>
        .checkout-page .card { border-radius: 12px; }
        .checkout-page .card-header { border-bottom: 1px solid #eee; }
        .checkout-page .list-group-item { border-color: #f1f1f1; }
        .checkout-page .form-check-input { width: 18px; height: 18px; }

        /* Custom modal (swap to Bootstrap modal if preferred) */
        .modal {
            position: fixed; inset: 0; background: rgba(0,0,0,.45);
            display: flex; justify-content: center; align-items: center; z-index: 1055;
        }
        .modal-content {
            background: #fff; width: 92%; max-width: 900px; border-radius: 12px;
            padding: 20px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,.25);
        }
        .right-modal { justify-content: flex-end; align-items: stretch; }
        .right-modal .modal-content {
            max-width: 420px; height: 100%; border-radius: 0; box-shadow: -6px 0 24px rgba(0,0,0,.18);
        }
        .modal-body { display: flex; flex-wrap: wrap; gap: 16px; }
        .modal-text, .modal-address { width: 48%; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #eee; padding-top: 12px; }

        .address-card {
            display: flex; align-items: flex-start; gap: 10px;
            border: 1px solid #e9ecef; border-radius: 10px; background: #fafafa; padding: 12px; margin-bottom: 10px;
        }
        .address-card:hover { background: #f6fff6; border-color: #cce5cc; }
        .address-card label { flex: 1; cursor: pointer; }
        @media (max-width: 992px) {
            .right-modal .modal-content { max-width: 100%; }
        }
        @media (max-width: 768px) {
            .modal-text, .modal-address { width: 100%; }
        }
    </style>
</x-app-layout>
