<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SellerController;
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
});

/* Products and Categories */
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class)->middleware('auth');

/* Authentication Routes */
require __DIR__ . '/auth.php';
