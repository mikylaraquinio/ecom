<x-app-layout>
    <div class="container mt-4">
        <div class="alert alert-success">
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your purchase. Your order has been placed successfully.</p>
        </div>

        <a href="{{ route('shop') }}" class="btn btn-primary">Continue Shopping</a>

        <!-- Recommended Products -->
        <div class="mt-4">
            <h2>Recommended Products</h2>
            <div id="product-list">
                @include('partials.product-list', ['products' => $products])
            </div>
        </div>
    </div>

    <script>
        // ✅ ADD TO CART FUNCTIONALITY (Now works after filtering)
        function attachAddToCartListeners() {
            document.querySelectorAll(".add-to-cart-modal").forEach(button => {
                button.addEventListener("click", function () {
                    let productId = this.dataset.productId;
                    let quantity = 1; // Default quantity to 1

                    fetch(`/cart/add/${productId}`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ quantity: quantity })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Product added to cart!");

                                // ✅ Close the modal
                                let modal = document.getElementById(`productModal${productId}`);
                                let modalInstance = bootstrap.Modal.getInstance(modal);
                                if (modalInstance) modalInstance.hide();
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        }

        attachAddToCartListeners(); // ✅ Attach event listeners on page load
    </script>
</x-app-layout>