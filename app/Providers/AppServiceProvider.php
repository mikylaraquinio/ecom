<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * ✅ Fix storage visibility on Hostinger or any shared hosting
         * Copies /storage/app/public → /public/storage if symlink is missing
         */
        if (!file_exists(public_path('storage'))) {
            File::makeDirectory(public_path('storage'), 0755, true);
            File::copyDirectory(storage_path('app/public'), public_path('storage'));
        }

        /**
         * ✅ Optional: Force HTTPS in production
         */
        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }

        /**
         * ✅ Share cart count globally
         */
        View::composer('*', function ($view) {
            $cartCount = 0;

            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
            }

            $view->with('cartCount', $cartCount);
        });
    }
}
