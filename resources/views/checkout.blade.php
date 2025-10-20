<x-app-layout>
    <div class="container my-5 checkout-page">
        <h2 class="mb-4 fw-semibold">Checkout</h2>

        @if($cartItems->count() > 0)
            @php
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
                                            @if($cartItem->id == 0)
                                                {{-- Buy Now flow: send product + qty --}}
                                                <input type="hidden" name="selected_items[0][product_id]"
                                                       value="{{ $cartItem->product->id }}">
                                                <input type="hidden" name="selected_items[0][quantity]"
                                                       value="{{ $cartItem->quantity }}">

                                                {{-- Hidden checkbox for JS subtotal calculation --}}
                                                <input type="checkbox" class="item-check d-none" checked
                                                       data-price="{{ $cartItem->product->price }}"
                                                       data-qty="{{ $cartItem->quantity }}"
                                                       data-seller="{{ $cartItem->product->user_id }}">
                                            @else
                                                {{-- Normal cart checkout --}}
                                                <input class="form-check-input mt-0 item-check" type="checkbox"
                                                       name="selected_items[]" value="{{ $cartItem->id }}" checked
                                                       data-price="{{ $cartItem->product->price }}"
                                                       data-qty="{{ $cartItem->quantity }}"
                                                       data-seller="{{ $cartItem->product->user_id }}">
                                            @endif
                                            <img src="{{ asset('storage/' . $cartItem->product->image) }}"
                                                 alt="{{ $cartItem->product->name }}" class="rounded border"
                                                 style="width:76px;height:76px;object-fit:cover;">
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

                            <div class="card-footer bg-white py-3 small text-muted" id="shipping-note">
                                <i class="bi bi-truck"></i>
                                Shipping: ₱50 per shop (auto-calculated)
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Address + Fulfillment + Payment + Summary -->
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
                                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center"
                                                 style="width:42px;height:42px;">
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
                                        <button type="button" class="btn btn-success w-100" id="add-address-btn">+ Add
                                            Address</button>
                                    @endif
                                </div>
                            </div>

                            <!-- Fulfillment (NEW) -->
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 fw-semibold">Fulfillment</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check d-flex align-items-start gap-2 mb-2">
                                        <input class="form-check-input mt-1" type="radio"
                                                name="fulfillment_method" id="fm_delivery" value="delivery" checked>
                                        <label class="form-check-label w-100" for="fm_delivery">
                                            <div class="fw-semibold">For delivery</div>
                                            <div class="text-muted small">Ship to the address above</div>
                                        </label>
                                        </div>

                                        <div class="form-check d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio"
                                                name="fulfillment_method" id="fm_pickup" value="pickup">
                                        <label class="form-check-label w-100" for="fm_pickup">
                                            <div class="fw-semibold">Pick up</div>
                                            <div class="text-muted small">No shipping fee. Pickup details will be sent after checkout.</div>
                                        </label>
                                    </div>

                                    <input type="hidden" name="fulfillment_method_final" id="fulfillment_method_final" value="delivery">
                                    
                                    {{-- Seller pickup addresses (shown only when "Pickup" is selected) --}}
                                    @if(!empty($pickupBySeller))
                                        <div id="pickupAddresses" class="mt-3 d-none">
                                            <div class="small text-muted mb-2">Pickup locations for your order</div>

                                            @foreach($pickupBySeller as $info)
                                            @php
                                                $addr = trim($info['address_line'] ?? '');
                                                // Fallback to seller name if address is empty (avoids broken map)
                                                $q = $addr !== '' ? $addr : ($info['name'] ?? 'Pickup');
                                            @endphp

                                            <div class="border rounded p-2 mb-2 small">
                                                <div class="fw-semibold">{{ $info['name'] }}</div>

                                                @if($addr !== '')
                                                <div class="text-muted">{{ $addr }}</div>
                                                @else
                                                <div class="text-muted fst-italic">Pickup address will be provided by the seller.</div>
                                                @endif

                                                @if(!empty($info['phone']))
                                                <div class="text-muted">Contact: {{ $info['phone'] }}</div>
                                                @endif

                                                {{-- Map (lazy-loaded when pickup is selected) --}}
                                                <div class="mt-2">
                                                <iframe
                                                    class="pickup-map w-100 rounded border"
                                                    height="180"
                                                    loading="lazy"
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    data-src="https://maps.google.com/maps?q={{ urlencode($q) }}&z=15&output=embed">
                                                </iframe>
                                                <div class="mt-1">
                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($q) }}" target="_blank" class="small">
                                                    Open in Google Maps
                                                    </a>
                                                </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Payment -->
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 fw-semibold">Payment Method</h5>
                                </div>
                                <div class="card-body payment-methods">
                                    <label class="form-check d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="payment_method" id="onlinePayment"
                                                   value="online" required>
                                            <span>Online Payment</span>
                                        </div>
                                    </label>
                                    <label class="form-check d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="payment_method" id="pm_cod"
                                                   value="cod">
                                            <span>Cash on Delivery</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 fw-semibold">Payment Details</h5>
                                </div>

                                <div class="card-body">

                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="text-start">Subtotal:</div>
                                        <div class="text-end">₱<span
                                                id="sum-subtotal">{{ number_format($subtotal, 2) }}</span></div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="text-start">Shipping Subtotal:</div>
                                        <div class="text-end">₱<span
                                                id="sum-shipping">{{ number_format($totalShipping, 2) }}</span></div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="text-start">Shipping Discount Subtotal:</div>
                                        <div class="text-end">- ₱<span
                                                id="sum-shipping-discount">{{ number_format($shippingDiscount ?? 0, 2) }}</span>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="d-flex justify-content-between fs-5 fw-bold">
                                        <div class="text-start">Total Payment</div>
                                        <div class="text-end text-success">
                                            ₱<span
                                                id="sum-total">{{ number_format(($subtotal + $totalShipping) - ($shippingDiscount ?? 0), 2) }}</span>
                                        </div>
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
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="First Last"
                            value="{{ auth()->user()->name }}"
                            readonly
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Enter phone number"
                            value="{{ auth()->user()->phone }}"
                            readonly
                        >
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
                        <input type="radio" name="selected_address" id="address-{{ $address->id }}"
                               value="{{ $address->id }}" class="form-check-input me-2"
                               @if(session('selected_address_id') == $address->id) checked @endif>
                        <label for="address-{{ $address->id }}" class="ms-1">
                            <strong>{{ $address->full_name }} - {{ $address->mobile_number }}</strong><br>
                            {{ $address->floor_unit_number }}, {{ $address->barangay }}, {{ $address->city }},
                            {{ $address->province }}<br>
                            {{ $address->notes }}
                        </label>

                        <button class="edit-address-btn btn btn-link p-0 small" type="button" data-id="{{ $address->id }}"
                                data-full_name="{{ $address->full_name }}" data-mobile_number="{{ $address->mobile_number }}"
                                data-notes="{{ $address->notes }}" data-floor_unit_number="{{ $address->floor_unit_number }}"
                                data-province="{{ $address->province }}" data-city="{{ $address->city }}"
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
            const fm = document.querySelector('input[name="fulfillment_method"]:checked')?.value || 'delivery';


            // New Fulfillment refs
            const fmDelivery = document.getElementById('fm_delivery');
            const fmPickup   = document.getElementById('fm_pickup');
            const fmHidden   = document.getElementById('fulfillment_method_final');

            const shippingNoteEl     = document.getElementById('shipping-note');
            const displayedAddressEl = document.getElementById('displayed-address');
            const editAddrBtnHeader  = document.getElementById('edit-address-btn');

            // ---------- Totals
            const itemChecks = document.querySelectorAll('.item-check');
            const sumSubtotal = document.getElementById('sum-subtotal');
            const sumShipping = document.getElementById('sum-shipping');
            const sumShippingDiscount = document.getElementById('sum-shipping-discount');
            const sumTotal = document.getElementById('sum-total');

            function isPickupMode() {
                return !!document.getElementById('fm_pickup')?.checked;
            }

            function recalcSummary() {
                let subtotal = 0;
                itemChecks.forEach(cb => {
                    if (!cb.checked) return;
                    const price = parseFloat(cb.dataset.price || '0');
                    const qty = parseInt(cb.dataset.qty || '1', 10);
                    subtotal += price * qty;
                });

                // base shipping & discount from DOM (server-rendered)…
                let shipping = parseFloat((sumShipping?.textContent || '0').replace(/,/g, '')) || 0;
                let shippingDiscount = parseFloat((sumShippingDiscount?.textContent || '0').replace(/,/g, '')) || 0;

                // …but in pickup mode, zero them out
                if (isPickupMode()) {
                    shipping = 0;
                    shippingDiscount = 0;
                }

                const total = subtotal + shipping - shippingDiscount;

                sumSubtotal.textContent = subtotal.toFixed(2);
                sumTotal.textContent = total.toFixed(2);
            }

            function applyFulfillmentMode() {
    const pickup = isPickupMode();
    fmHidden.value = pickup ? 'pickup' : 'delivery';

    // Toggle address card
    if (pickup) {
        displayedAddressEl?.classList.add('address-disabled');
        if (editAddrBtnHeader) {
            editAddrBtnHeader.disabled = true;
            editAddrBtnHeader.setAttribute('aria-disabled', 'true');
        }
        if (shippingNoteEl) {
            shippingNoteEl.classList.add('text-success');
            shippingNoteEl.innerHTML = `<i class="bi bi-bag-check"></i> Pickup: No shipping fee`;
        }
    } else {
        displayedAddressEl?.classList.remove('address-disabled');
        if (editAddrBtnHeader) {
            editAddrBtnHeader.disabled = false;
            editAddrBtnHeader.removeAttribute('aria-disabled');
        }
        if (shippingNoteEl) {
            shippingNoteEl.classList.remove('text-success');
            shippingNoteEl.innerHTML = `<i class="bi bi-truck"></i> Shipping: ₱50 per shop (auto-calculated)`;
        }
    }

    // ✅ Handle pickup map display
    const pickupEl = document.getElementById('pickupAddresses');
    if (pickupEl) {
        pickupEl.classList.toggle('d-none', !pickup);

        // Lazy-load maps only when pickup selected
        if (pickup) {
            const iframes = pickupEl.querySelectorAll('iframe.pickup-map');
            if (iframes.length === 0) {
                console.warn('⚠️ No pickup maps found in DOM');
            }
            iframes.forEach(iframe => {
                if (!iframe.src && iframe.dataset.src) {
                    iframe.src = iframe.dataset.src;
                    iframe.style.opacity = '1';
                }
            });
        } else {
            // Hide maps when returning to delivery
            pickupEl.querySelectorAll('iframe.pickup-map').forEach(iframe => {
                iframe.removeAttribute('src');
                iframe.style.opacity = '0';
            });
        }
    }

    // ✅ Fix shipping subtotal
    const shippingSubtotalEl = document.getElementById('sum-shipping');
    if (pickup) {
        shippingSubtotalEl.textContent = '0.00';
    }

    recalcSummary();
}

            itemChecks.forEach(cb => cb.addEventListener('change', recalcSummary));

            // Bind fulfillment changes
            fmDelivery?.addEventListener('change', applyFulfillmentMode);
            fmPickup?.addEventListener('change', applyFulfillmentMode);

            // Initial calc + mode
            recalcSummary();
            applyFulfillmentMode();

            // ---------- Edit Address Modal
            editAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                // Do not open when pickup selected
                if (isPickupMode()) return;
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

            // 🔁 Handle address selection dynamically
document.querySelectorAll('input[name="selected_address"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const selectedId = this.value;
        const card = this.closest('.address-card');
        const lbl = card.querySelector('label');
        const lines = lbl.innerHTML.split('<br>');
        const header = lines[0] ?? '';
        const address1 = lines[1] ?? '';
        const notes = lines[2] ?? '';

        // Update hidden address_id input
        const dest = document.querySelector('input[name="address_id"]');
        if (dest) dest.value = selectedId;

        // Update displayed address on the checkout card instantly
        const display = document.getElementById('displayed-address');
        display.innerHTML = `
            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="flex-grow-1 small">
                <div class="fw-semibold">${header}</div>
                <div class="text-muted">${address1}</div>
                ${notes ? `<div class="text-muted fst-italic">${notes}</div>` : ""}
            </div>
        `;

        // --- 💾 Save selected address to session
        fetch(`{{ route('checkout.saveSelectedAddress') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ address_id: selectedId })
        });

        // --- 🚚 Show spinner while updating shipping
        const shippingSpan = document.getElementById('sum-shipping');
        shippingSpan.textContent = '...';

        // --- 🔄 Recalculate shipping dynamically
        fetch(`{{ route('checkout.recalcShipping') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                address_id: selectedId,
                selected_items: Array.from(
                    document.querySelectorAll('input[name="selected_items[]"]:checked')
                ).map(i => i.value),
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update shipping subtotal
                shippingSpan.textContent = data.totalShipping.toFixed(2);

                // Recalculate total summary live
                recalcSummary();
            } else {
                shippingSpan.textContent = '0.00';
                recalcSummary();
            }
        })
        .catch(() => {
            shippingSpan.textContent = '0.00';
            recalcSummary();
        });

        // Close the side modal after selecting
        const modal = document.getElementById('editAddressModal');
        if (modal) modal.style.display = 'none';
    });
});


            // Save (create/update) address
            saveAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();

                const payload = {
                    full_name: document.querySelector('input[placeholder="First Last"]').value || "{{ auth()->user()->name }}",
                    mobile_number: document.querySelector('input[placeholder="Enter phone number"]').value || "{{ auth()->user()->phone }}",
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
            proceedBtn?.addEventListener('click', async function (e) {
                e.preventDefault();

                // 🧩 Disable button immediately
                if (this.disabled) return;
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> Placing Order...`;

                const fulfillment = document.getElementById('fulfillment_method_final')?.value || 'delivery';
                const addressId = document.querySelector('input[name="address_id"]')?.value;

                if (fulfillment === 'delivery' && !addressId) {
                    alert('Please select a shipping address.');
                    resetButton();
                    return;
                }

                const pm = document.querySelector('input[name="payment_method"]:checked');
                if (!pm) {
                    alert('Please select a payment method.');
                    resetButton();
                    return;
                }

                // Collect selected items
                let selectedItems = Array.from(
                    document.querySelectorAll('input[name="selected_items[]"]:checked')
                ).map(i => i.value);

                if (!selectedItems.length) {
                    const buyNowProductId = document.querySelector('input[name="selected_items[0][product_id]"]')?.value;
                    const buyNowQty = document.querySelector('input[name="selected_items[0][quantity]"]')?.value;
                    if (buyNowProductId && buyNowQty) {
                    selectedItems = [{ product_id: buyNowProductId, quantity: buyNowQty }];
                    }
                }

                if (!selectedItems.length) {
                    alert('Please select at least one product.');
                    resetButton();
                    return;
                }

                try {
                    const r = await fetch("{{ route('checkout.process') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({
                        address_id: addressId || null,
                        selected_items: selectedItems,
                        payment_method: pm.value,
                        fulfillment_method: fulfillment,
                    }),
                    });

                    const data = await r.json().catch(() => ({}));

                    if (r.ok && data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                    } else {
                    alert(data.message || 'Something went wrong placing the order.');
                    resetButton();
                    }
                } catch (err) {
                    console.error(err);
                    alert('Failed to place order. Please try again.');
                    resetButton();
                }

                // helper
                function resetButton() {
                    proceedBtn.disabled = false;
                    proceedBtn.innerHTML = originalText;
                }
            });

            // Helpers
            function clearAddressForm() {
                document.querySelectorAll('#addressModal input').forEach(i => i.value = '');
            }
        });
    </script>

    <style>
        .checkout-page .card {
            border-radius: 12px;
            display: block;
            align-items: unset !important;
            text-align: left;
        }

        .checkout-page .card-body>.d-flex { width: 100%; }
        .checkout-page .card-body .text-end { text-align: right !important; }
        .checkout-page .card-header { border-bottom: 1px solid #eee; }
        .checkout-page .list-group-item { border-color: #f1f1f1; }
        .checkout-page .form-check { display: flex; align-items: flex-start; gap: .5rem; }
        .checkout-page .form-check-input { width: 18px; height: 18px; margin: 0; }
        .checkout-page .form-check-input.mt-1 { margin-top: .2rem; } /* tiny nudge */
        .checkout-page .form-check-label { line-height: 1.25; }

        /* Payment methods alignment */
        .payment-methods .form-check { width: 100%; cursor: pointer; }
        .payment-methods span { flex: 1; text-align: left; }
        .checkout-page .form-check-input { width: 18px; height: 18px; margin: 0; }

        /* Custom modal (swap to Bootstrap modal if preferred) */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1055;
        }

        .modal-content {
            background: #fff;
            width: 92%;
            max-width: 900px;
            border-radius: 12px;
            padding: 20px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        }

        .right-modal { justify-content: flex-end; align-items: stretch; }
        .right-modal .modal-content {
            max-width: 420px;
            height: 100%;
            border-radius: 0;
            box-shadow: -6px 0 24px rgba(0, 0, 0, .18);
        }

        .modal-body { display: flex; flex-wrap: wrap; gap: 16px; }
        .modal-text, .modal-address { width: 48%; }

        .modal-actions {
            display: flex; justify-content: flex-end; gap: 10px;
            border-top: 1px solid #eee; padding-top: 12px;
        }

        .address-card {
            display: flex; align-items: flex-start; gap: 10px;
            border: 1px solid #e9ecef; border-radius: 10px; background: #fafafa;
            padding: 12px; margin-bottom: 10px;
        }

        .address-card:hover { background: #f6fff6; border-color: #cce5cc; }
        .address-card label { flex: 1; cursor: pointer; }

        /* Dim/disable address card when pickup selected */
        .address-disabled {
            opacity: .6;
            pointer-events: none;
        }

        @media (max-width: 992px) { .right-modal .modal-content { max-width: 100%; } }
        @media (max-width: 768px) { .modal-text, .modal-address { width: 100%; } }

        .mt-3 {
            transition: opacity 0.3s ease;
        }

        .mt-3[style*="display: none"] {
            opacity: 0;
        }

    </style>
</x-app-layout>
