<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

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



/*Product*/
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::get('/shop', [ProductController::class, 'index'])->name('shop');

/*user*/

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/sell', [ProfileController::class, 'sell'])->name('profile.sell');
});


Route::get('/user_profile', function () {
    return view('user_profile');
})->middleware(['auth', 'verified'])->name('user_profile');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');

require __DIR__.'/auth.php';
