<?php

if (!function_exists('image_url')) {
    function image_url($path) {
        if (!$path) {
            return asset('assets/default.png');
        }

        // Normalize path (remove duplicate folders)
        $clean = str_replace(['public/', 'storage/'], '', $path);

        // ✅ Local (symlink exists)
        if (file_exists(public_path('storage/' . $clean))) {
            return asset('storage/' . $clean);
        }

        // ✅ Hostinger fallback (no symlink)
        return url('storage/' . $clean);
    }
}
