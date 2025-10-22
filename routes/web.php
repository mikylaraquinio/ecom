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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FollowController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/', [CategoryController::class, 'index'])->name('welcome');

// Optional alias for /welcome — loads the same controller
Route::get('/welcome', [CategoryController::class, 'index']);


// Shop Page

Route::get('/shop', [ProductController::class, 'index'])->name('shop');
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
    Route::get('/sell', [ProfileController::class, 'sell'])->name('seller.sell');
    Route::post('/profile/picture', [ProfileController::class, 'updatePicture'])
        ->name('profile.picture.update');

    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::get('/user_profile', function () {
        $categories = Category::all(); // Fetch all categories
        return view('user_profile', compact('categories'));
    })->middleware(['auth', 'verified'])->name('user_profile');

    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
});
Route::post('/profile/update-picture', [ProfileController::class, 'updatePicture'])
    ->name('profile.updatePicture');

Route::get('/user_profile', [ProfileController::class, 'showProfile'])->middleware(['auth'])->name('user_profile');
Route::patch('/buyer/order/{id}/cancel', [ProfileController::class, 'cancelOrder'])->name('buyer.cancelOrder');

Route::patch('/orders/{id}/confirm-receipt', [ProfileController::class, 'confirmReceipt'])
    ->name('buyer.confirmReceipt')
    ->middleware('auth');


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
});

Route::get('/autocomplete', [ProductController::class, 'autocomplete']);

/*Cart*/
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/shipping', [CartController::class, 'shipping'])->name('cart.shipping');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/bulk-delete', [CartController::class, 'bulkDelete'])->name('cart.bulkDelete');
Route::post('/cart/checkout-selected', [CartController::class, 'checkoutSelected'])->name('cart.checkoutSelected');
Route::post('/checkout', [CartController::class, 'process'])->name('checkout.process');

//Shipping fee
Route::post('/checkout/recalc-shipping', [CheckoutController::class, 'recalcShipping'])
    ->name('checkout.recalcShipping');

// Checkout Routes
Route::post('/checkout', [CheckoutController::class, 'prepareCheckout'])->name('checkout.prepare');
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.show');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process'); // Corrected this
Route::post('/checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('checkout.saveAddress');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/getAddress', [CheckoutController::class, 'getAddress'])->name('checkout.getAddress');
Route::put('/checkout/updateAddress/{id}', [CheckoutController::class, 'updateAddress'])->name('checkout.updateAddress');
Route::post('/checkout/saveSelectedAddress', [CheckoutController::class, 'saveSelectedAddress'])->name('checkout.saveSelectedAddress');

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

Route::post('/orders/{order}/cancel', [App\Http\Controllers\CartController::class, 'cancel'])
    ->name('orders.cancel');

Route::get('/seller/orders/{order}/cod-invoice', [SellerController::class, 'viewCodInvoice'])
    ->name('seller.codInvoice');

Route::post('/seller/orders/{id}/generate-invoice', [SellerController::class, 'generateInvoice'])->name('seller.generateInvoice');
Route::get('/seller/orders/{id}/invoice', [SellerController::class, 'viewInvoice'])->name('seller.viewInvoice');


Route::middleware(['auth'])->group(function () {
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');


Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

/*notifications */

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

/* Chat */
Route::get('/chat/{receiverId?}', [ChatController::class, 'index'])->name('chat');   // optional (kept for safety)
Route::post('/chat/send/{receiverId}', [ChatController::class, 'store'])->name('chat.send');

// NEW: lightweight data endpoints for the widget (JSON)
Route::get('/chat/data/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
Route::get('/chat/data/messages/{receiverId}', [ChatController::class, 'messages'])->name('chat.messages');

Route::get('/seller/revenue-data', [SellerController::class, 'revenueData'])->name('seller.revenueData');
Route::get('/invoice', [PaymentController::class, 'createInvoice'])->name('invoice.create');
Route::post('/xendit/webhook', [CheckoutController::class, 'handleXenditWebhook']);

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::post('/ai-chat', [AIChatController::class, 'chat'])->name('ai.chat');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/sellers/pending', [AdminController::class, 'pendingSellers'])->name('admin.sellers.pending');
    Route::patch('/admin/sellers/{id}/approve', [AdminController::class, 'approveSeller'])->name('admin.sellers.approve');
    Route::patch('/admin/sellers/{id}/deny', [AdminController::class, 'denySeller'])->name('admin.sellers.deny');
});


// For users
Route::middleware(['auth'])->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

Route::middleware(['auth'])->group(function () {
    Route::patch('/admin/reports/{id}/status', [ReportController::class, 'updateStatus'])
        ->name('admin.reports.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        $reports = \App\Models\Report::with('user')->latest()->get();
        return view('admin.dashboard', compact('reports'));
    })->name('admin.dashboard');
});

// Product view page
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Seller shop page
Route::get('/shop/{seller}', [ShopController::class, 'view'])->name('shop.view');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

Route::get('/email/verify', function () {
    return view('auth.verify-email'); // show the verify page
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // mark email as verified
    return redirect('/welcome')->with('status', 'Your email has been verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/verify-notice', function () {
    return view('auth.verify-notice');
})->name('verify.notice.guest');

//Analytics
Route::get('/seller/analytics', [SellerController::class, 'analytics'])->name('seller.analytics');

/* Authentication Routes */
require __DIR__ . '/auth.php';

