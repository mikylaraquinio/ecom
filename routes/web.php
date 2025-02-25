<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SellerController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/welcome', function () {
    return view('pages.home');
})->middleware(['auth']);
Route::get('/welcome', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('welcome');
Route::get('/shop', function () {
    return view('shop');
})->middleware(['auth', 'verified'])->name('shop');


/*farmers*/
Route::get('/farmers/sell', [ProfileController::class, 'sell'])->name('farmers.sell');
Route::post('/profile/sell', [ProfileController::class, 'storeSeller'])->name('profile.sell');

/*user*/

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/sell', [ProfileController::class, 'sell'])->name('profile.sell');

    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');


    Route::get('/user-profile', function () {
        return view('user_profile');
    })->name('user.profile');
});


Route::get('/user_profile', function () {
    return view('user_profile');
})->middleware(['auth', 'verified'])->name('user_profile');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');

require __DIR__.'/auth.php';



/* Farmers (Seller Registration) */
Route::middleware(['auth'])->group(function () {
    Route::get('/farmers/sell', [SellerController::class, 'sell'])->name('farmers.sell');
    Route::post('/farmers/sell/store', [SellerController::class, 'storeSeller'])->name('farmers.storeSeller');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    });
});

/* Products and Categories */
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::get('/shop', [ProductController::class, 'index'])->name('shop');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});

require __DIR__.'/auth.php';
