<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;
use App\Models\Category;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/', function () {
    return view('welcome');
});

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
Route::patch('/buyer/order/{id}/cancel', [ProfileController::class, 'cancelOrder'])->name('buyer.cancelOrder');





/* Farmers (Seller Registration) */
Route::middleware(['auth'])->group(function () {
    Route::get('/farmers/sell', [SellerController::class, 'sell'])->name('farmers.sell');
    Route::post('/farmers/store-seller', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');

    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    });
    Route::post('/update-seller', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');
});
Route::patch('/seller/order/{id}/approve-cancel', [SellerController::class, 'approveCancel'])->name('seller.approveCancel');
Route::patch('/seller/order/{id}/deny-cancel', [SellerController::class, 'denyCancel'])->name('seller.denyCancel');


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
Route::post('/checkout/saveSelectedAddress', [CheckoutController::class, 'saveSelectedAddress'])->name('checkout.saveSelectedAddress');
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


Route::middleware('auth')->post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');


Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/chat/{receiverId?}', [ChatController::class, 'index'])->name('chat');
Route::post('/chat/{receiverId}', [ChatController::class, 'store'])->name('chat.send');










/* Authentication Routes */
require __DIR__ . '/auth.php';
