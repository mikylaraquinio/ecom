<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('image_url')) {
    function image_url($path) {
        if (!$path) {
            return asset('assets/default.png');
        }

        // Check if file exists in storage
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        // Fallback if absolute URL already or CDN
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Fallback default
        return asset('assets/default.png');
    }
}
