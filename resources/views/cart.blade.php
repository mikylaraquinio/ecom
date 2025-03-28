<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4">Shopping Cart</h2>

        @if($cartItems->count() > 0)
            <div class="row">
                <div class="col-md-8">
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
                                                    class="product-checkbox" data-price="{{ $cartItem->product->price }}"
                                                    data-product-id="{{ $cartItem->id }}"
                                                    data-product-name="{{ $cartItem->product->name }}">
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

                        <div id="selected-products" class="mt-3 text-start"></div>

                        <button 
                            class="btn btn-success btn-block mt-3 proceed-to-checkout" 
                            id="checkout-btn"
                            disabled>
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        @else
            <p class="text-muted">Your cart is empty. <a href="{{ route('shop') }}">Continue Shopping</a></p>
        @endif
    </div>
    
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAllCheckbox = document.getElementById("select-all");
        const deleteSelectedBtn = document.getElementById("delete-selected");
        const totalPriceEl = document.getElementById("total-price");
        const checkoutBtn = document.getElementById("checkout-btn");
        const loadingScreen = document.getElementById("loading-screen");
        const cartBody = document.querySelector("tbody");

        function showLoading() {
            loadingScreen?.classList.remove("d-none");
        }

        function hideLoading() {
            loadingScreen?.classList.add("d-none");
        }

        function updateTotal() {
            let total = 0;
            let hasCheckedItems = false;
            const selectedProductsContainer = document.getElementById("selected-products");
            selectedProductsContainer.innerHTML = "";

            document.querySelectorAll(".product-checkbox:checked").forEach(cb => {
                const row = cb.closest("tr");
                const price = parseFloat(cb.dataset.price);
                const quantity = parseInt(row.querySelector(".quantity-input").value);
                total += price * quantity;
                hasCheckedItems = true;

                const productName = row.querySelector("td:nth-child(2)").innerText.trim();
                const productItem = document.createElement("div");
                productItem.textContent = `${productName} × ${quantity}`;
                selectedProductsContainer.appendChild(productItem);
            });

            totalPriceEl.textContent = total.toFixed(2);
            deleteSelectedBtn.disabled = !hasCheckedItems;
            checkoutBtn.disabled = !hasCheckedItems;
        }

        function checkIfCartIsEmpty() {
            if (!cartBody.querySelector("tr")) {
                window.location.reload();
            }
        }

        selectAllCheckbox?.addEventListener("change", function () {
            document.querySelectorAll(".product-checkbox").forEach(cb => cb.checked = this.checked);
            updateTotal();
        });

        document.addEventListener("change", function (e) {
            if (e.target.classList.contains("product-checkbox")) {
                updateTotal();
                selectAllCheckbox.checked = [...document.querySelectorAll(".product-checkbox")].every(cb => cb.checked);
            }
        });

        deleteSelectedBtn.addEventListener("click", function () {
            const selectedIds = [...document.querySelectorAll(".product-checkbox:checked")].map(cb => cb.value);
            if (selectedIds.length === 0) return alert("Select at least one item to delete.");
            if (!confirm("Are you sure you want to delete selected items?")) return;

            showLoading();

            fetch("{{ route('cart.bulkDelete') }}", {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ selected_items: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    selectedIds.forEach(id => document.getElementById(`cart-item-${id}`)?.remove());
                    updateTotal();
                    checkIfCartIsEmpty();
                } else {
                    alert("Error deleting items.");
                }
            })
            .catch(err => console.error("Error:", err))
            .finally(() => hideLoading());
        });

        document.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-item")) {
                const itemId = e.target.dataset.id;
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`cart-item-${itemId}`)?.remove();
                        updateTotal();
                        checkIfCartIsEmpty();
                    } else {
                        alert(data.message || "Error removing item.");
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("Something went wrong. Please try again.");
                })
                .finally(() => hideLoading());
            }

            if (e.target.classList.contains("increment-btn")) {
                const itemId = e.target.dataset.id;
                const inputField = document.querySelector(`.quantity-input[data-id='${itemId}']`);
                const newQuantity = parseInt(inputField.value) + 1;
                updateQuantity(itemId, newQuantity, inputField);
            }

            if (e.target.classList.contains("decrement-btn")) {
                const itemId = e.target.dataset.id;
                const inputField = document.querySelector(`.quantity-input[data-id='${itemId}']`);
                const newQuantity = Math.max(1, parseInt(inputField.value) - 1);
                updateQuantity(itemId, newQuantity, inputField);
            }
        });

        function updateQuantity(itemId, newQuantity, inputField) {
            showLoading();

            fetch(`{{ route('cart.update', '') }}/${itemId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    inputField.value = data.new_quantity;
                    const price = parseFloat(document.querySelector(`.product-checkbox[value='${itemId}']`).dataset.price);
                    document.querySelector(`#cart-item-${itemId} .subtotal`).textContent = `₱${(price * data.new_quantity).toFixed(2)}`;
                    updateTotal();
                } else {
                    alert(data.message || "Error updating quantity.");
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Something went wrong while updating quantity.");
            })
            .finally(() => hideLoading());
        }

        updateTotal();
    });
</script>

<script>
    document.getElementById('checkout-btn').addEventListener('click', function () {
        const selectedItems = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(checkbox => checkbox.value);

        if (selectedItems.length === 0) {
            alert('Please select at least one product.');
            return;
        }

        fetch('/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                selected_items: selectedItems
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert('Something went wrong. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
</script>
</x-app-layout>
