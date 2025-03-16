<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4">Shopping Cart</h2>

        @if($cartItems->count() > 0)
            <div class="row">
                <div class="col-md-8">
                    {{-- Removed action and method since we're handling via JS --}}
                    <form id="cart-form" onsubmit="return false;">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <input type="checkbox" id="select-all"> Select All
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" id="delete-selected" disabled>Remove</button>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @foreach($cartItems as $cartItem)
                                    @if ($cartItem->product)
                                        @php 
                                            $subtotal = $cartItem->product->price * $cartItem->quantity;
                                            $total += $subtotal;
                                        @endphp
                                        <tr id="cart-item-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}">
                                            <td>
                                                <input type="checkbox" name="selected_items[]" value="{{ $cartItem->id }}"
                                                    class="product-checkbox" data-price="{{ $cartItem->product->price }}">
                                            </td>
                                            <td>
                                                <img src="{{ asset('storage/' . $cartItem->product->image) }}" alt="{{ $cartItem->product->name }}" width="50">
                                                {{ $cartItem->product->name }}
                                            </td>
                                            <td>₱{{ number_format($cartItem->product->price, 2) }}</td>
                                            <td>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm decrement-btn" data-id="{{ $cartItem->id }}">-</button>
                                                    <input type="text" class="form-control text-center quantity-input" value="{{ $cartItem->quantity }}" data-id="{{ $cartItem->id }}" readonly>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm increment-btn" data-id="{{ $cartItem->id }}">+</button>
                                                </div>
                                            </td>
                                            <td class="subtotal">₱{{ number_format($subtotal, 2) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item" data-id="{{ $cartItem->id }}">Remove</button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="col-md-4">
                    <div class="card p-3 text-center position-sticky shadow" style="top: 20px; min-height: 300px;">
                        <h4>Total: ₱<span id="total-price">{{ number_format($total, 2) }}</span></h4>

                        <!-- 🟢 Selected product names will be shown here -->
                        <div id="selected-products" class="mt-3 text-start">
                            <!-- JS will dynamically insert product names -->
                        </div>

                        <button class="btn btn-success btn-block mt-3" id="checkout-btn" data-bs-toggle="modal" data-bs-target="#checkoutModal" disabled>
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

            <div id="loading-screen" class="loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @else
            <p class="text-muted">Your cart is empty. <a href="{{ route('shop') }}">Continue Shopping</a></p>
        @endif
    </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAllCheckbox = document.getElementById("select-all");
        const productCheckboxes = document.querySelectorAll(".product-checkbox");
        const deleteSelectedBtn = document.getElementById("delete-selected");
        const totalPriceEl = document.getElementById("total-price");
        const checkoutBtn = document.getElementById("checkout-btn");
        const loadingScreen = document.getElementById("loading-screen");
        const cartBody = document.querySelector("tbody"); // For checking if cart is empty

        // Show loading
        function showLoading() {
            loadingScreen.classList.remove("d-none");
        }

        // Hide loading
        function hideLoading() {
            loadingScreen.classList.add("d-none");
        }

        // Update total price
        function updateTotal() {
            let total = 0;
            let hasCheckedItems = false;
            const selectedProductsContainer = document.getElementById("selected-products");
            selectedProductsContainer.innerHTML = ""; // Clear previous list

            productCheckboxes.forEach(cb => {
                if (cb.checked) {
                    const row = cb.closest("tr");
                    const price = parseFloat(cb.dataset.price);
                    const quantity = parseInt(row.querySelector(".quantity-input").value);
                    total += price * quantity;
                    hasCheckedItems = true;

                    // Get product name
                    const productName = row.querySelector("td:nth-child(2)").innerText.trim();

                    // Append product name to container
                    const productItem = document.createElement("div");
                    productItem.textContent = `${productName} × ${quantity}`;
                    selectedProductsContainer.appendChild(productItem);
                }
            });

            totalPriceEl.textContent = total.toFixed(2);
            deleteSelectedBtn.disabled = !hasCheckedItems;
            checkoutBtn.disabled = !hasCheckedItems;
        }


        // Check if cart is empty and reload
        function checkIfCartIsEmpty() {
            if (cartBody.children.length === 0) {
                window.location.reload(); // Reload cart page
            }
        }

        // Select all checkbox
        selectAllCheckbox?.addEventListener("change", function () {
            productCheckboxes.forEach(cb => cb.checked = this.checked);
            updateTotal();
        });

        // Checkbox change (individual checkbox logic)
        productCheckboxes.forEach(cb => cb.addEventListener("change", function () {
            updateTotal();

            // If all checkboxes are checked, check "Select All"
            const allChecked = [...productCheckboxes].every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        }));

        // Delete selected items
        deleteSelectedBtn.addEventListener("click", function () {
            const selectedIds = [...document.querySelectorAll(".product-checkbox:checked")].map(cb => cb.value);
            if (selectedIds.length === 0) return alert("Select at least one item to delete.");

            if (!confirm("Are you sure you want to delete selected items?")) return;

            showLoading();

            fetch("{{ route('cart.bulkDelete') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE", selected_items: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    selectedIds.forEach(id => {
                        document.getElementById(`cart-item-${id}`)?.remove();
                    });
                    updateTotal();
                    checkIfCartIsEmpty(); // Check after deletion
                } else {
                    alert("Error deleting items.");
                }
            })
            .catch(err => console.error(err))
            .finally(() => hideLoading());
        });

        // Single item remove
        document.querySelectorAll(".remove-item").forEach(button => {
            button.addEventListener("click", function () {
                const itemId = this.dataset.id;
                if (!confirm("Are you sure you want to remove this item?")) return;

                showLoading();

                fetch(`/cart/remove/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById(`cart-item-${itemId}`)?.remove();
                        updateTotal();
                        checkIfCartIsEmpty(); // Check after single deletion
                    } else {
                        alert(data.message || "Error removing item.");
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("Something went wrong. Please try again.");
                })
                .finally(() => hideLoading());
            });
        });

        // Quantity increment
        document.querySelectorAll(".increment-btn").forEach(button => {
            button.addEventListener("click", function () {
                const itemId = this.dataset.id;
                const inputField = document.querySelector(`.quantity-input[data-id='${itemId}']`);
                const newQuantity = parseInt(inputField.value) + 1;
                updateQuantity(itemId, newQuantity, inputField);
            });
        });

        // Quantity decrement
        document.querySelectorAll(".decrement-btn").forEach(button => {
            button.addEventListener("click", function () {
                const itemId = this.dataset.id;
                const inputField = document.querySelector(`.quantity-input[data-id='${itemId}']`);
                const newQuantity = Math.max(1, parseInt(inputField.value) - 1);
                updateQuantity(itemId, newQuantity, inputField);
            });
        });

        // Update quantity AJAX
        function updateQuantity(itemId, newQuantity, inputField) {
            showLoading();

            fetch(`{{ route('cart.update', '') }}/${itemId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json" // 🔥 Important to match JSON response
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    inputField.value = data.new_quantity; // Use backend-confirmed value

                    const price = parseFloat(document.querySelector(`.product-checkbox[value='${itemId}']`).dataset.price);
                    document.querySelector(`#cart-item-${itemId} .subtotal`).textContent = `₱${(price * data.new_quantity).toFixed(2)}`;

                    // Update total price after quantity change
                    updateTotal();
                } else {
                    alert(data.message || "Error updating quantity.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Something went wrong while updating quantity.");
            })
            .finally(() => hideLoading());
        }

        // Initial call
        updateTotal();
    });
</script>
</x-app-layout>
