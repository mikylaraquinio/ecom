<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\Category;

Route::get('/', function () {
    return view('welcome');
});

// Ensure that "home.blade.php" is correctly referenced
Route::get('/welcome', function () {
    return view('welcome'); // ✅ Correct
})->middleware(['auth', 'verified'])->name('welcome');

// Shop Page
Route::get('/shop', [ProductController::class, 'index'])->middleware(['auth', 'verified'])->name('shop');
Route::get('/myshop', [SellerController::class, 'index'])->name('myshop');
Route::put('/shop/{shop}', [SellerController::class, 'update'])->name('shop.update');
// Show the create shop form
Route::get('/shop/create', [SellerController::class, 'create'])->name('shop.create');

// Store the shop data after form submission
Route::post('/shop', [SellerController::class, 'store'])->name('shop.store');

/* Farmers */
Route::middleware(['auth'])->group(function () {
    Route::get('/farmers/sell', [ProfileController::class, 'sell'])->name('farmers.sell');
    Route::post('/profile/sell', [ProfileController::class, 'storeSeller'])->name('profile.sell');
});

/* User Routes */
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/sell', [ProfileController::class, 'sell'])->name('profile.sell');

    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::get('/user_profile', function () {
        $categories = Category::all(); // Fetch all categories
        return view('user_profile', compact('categories'));
    })->middleware(['auth', 'verified'])->name('user_profile');

    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/profile/update-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.updatePicture');
});
Route::get('/user_profile', [ProfileController::class, 'showProfile'])->middleware(['auth', 'verified'])->name('user_profile');




/* Farmers (Seller Registration) */
Route::middleware(['auth'])->group(function () {
    Route::get('/farmers/sell', [SellerController::class, 'sell'])->name('farmers.sell');
    Route::post('/farmers/sell/store', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');

    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    });
    Route::post('/update-seller', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');
});

// Categories
Route::resource('categories', CategoryController::class);
Route::get('/categories/{id}/subcategories', [CategoryController::class, 'getSubcategories']);
Route::get('/subcategories/{id}/products', [ProductController::class, 'getProductsBySubcategory']);

// Products
Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('/my-products', [ProductController::class, 'myProducts'])->name('products.myProducts');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
});

Route::get('/autocomplete', [ProductController::class, 'autocomplete']);

/*Cart*/
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/bulk-delete', [CartController::class, 'bulkDelete'])->name('cart.bulkDelete');
Route::post('/cart/checkout-selected', [CartController::class, 'checkoutSelected'])->name('cart.checkoutSelected');
Route::post('/checkout', [CartController::class, 'process'])->name('checkout.process');

// Checkout Routes
Route::post('/checkout', [CheckoutController::class, 'prepareCheckout'])->name('checkout.prepare');
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.show');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process'); // Corrected this
Route::post('/checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('checkout.saveAddress');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/getAddress', [CheckoutController::class, 'getAddress'])->name('checkout.getAddress');
Route::put('/checkout/updateAddress/{id}', [CheckoutController::class, 'updateAddress'])->name('checkout.updateAddress');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');




/*Orders*/
Route::get('/seller/orders', [SellerController::class, 'incomingOrders'])->name('seller.orders');
Route::patch('/seller/orders/update/{id}', [SellerController::class, 'updateOrderStatus'])->name('seller.orders.update');
Route::get('/user-profile', [CartController::class, 'showUserProfile'])->name('user.profile');
Route::middleware('auth')->group(function () {
    Route::delete('/order/{order}/cancel', [CartController::class, 'cancel'])->name('order.cancel');
    Route::get('/order/{order}/edit', [CartController::class, 'edit'])->name('order.edit');
});
Route::get('/my-orders', [SellerController::class, 'myOrders'])->name('buyer.orders')->middleware('auth');
Route::patch('/seller/orders/{id}/update', [SellerController::class, 'updateOrderStatus'])->name('seller.updateOrderStatus');
Route::get('/orders/{id}/confirm', [SellerController::class, 'confirmReceipt'])->name('buyer.confirm-receipt');


/* Authentication Routes */
require __DIR__ . '/auth.php';
