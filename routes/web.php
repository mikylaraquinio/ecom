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

/* Farmers (Seller Registration) */
Route::middleware(['auth'])->group(function () {
    Route::get('/farmers/sell', [SellerController::class, 'sell'])->name('farmers.sell');
    Route::post('/farmers/sell/store', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');

    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    });
    Route::post('/update-seller', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');

});

/* Products and Categories */
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class)->middleware('auth');
Route::get('/my-products', [ProductController::class, 'myProducts'])->name('products.myProducts')->middleware('auth');
Route::get('/autocomplete', [ProductController::class, 'autocomplete']);


/*Cart*/
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/checkout', [CartController::class, 'process'])->name('checkout.process');

/*Orders*/
Route::get('/seller/orders', [SellerController::class, 'incomingOrders'])->name('seller.orders');
Route::patch('/seller/orders/update/{id}', [SellerController::class, 'updateOrderStatus'])->name('seller.orders.update');
Route::get('/user-profile', [CartController::class, 'showUserProfile'])->name('user.profile');
Route::middleware('auth')->group(function () {
    Route::delete('/order/{order}/cancel', [CartController::class, 'cancel'])->name('order.cancel');
    Route::get('/order/{order}/edit', [CartController::class, 'edit'])->name('order.edit');
});


Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

/* Authentication Routes */
require __DIR__ . '/auth.php';
