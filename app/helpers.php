<?php

if (!function_exists('getCategoryIcon')) {
    function getCategoryIcon($categoryName, $parentCategoryName = null)
    {
        $icons = [
            'Grains & Cereals' => '🌾',
            'Vegetables' => '🥦',
            'Fruits' => '🍎',
            'Herbs & Spices' => '🌿',
            'Livestock' => '🐄',
            'Poultry' => '🐔'
        ];

        // 1️⃣ If the category has its own icon, return it
        if (isset($icons[$categoryName])) {
            return $icons[$categoryName];
        }

        // 2️⃣ If it's a subcategory, inherit from the parent category
        if ($parentCategoryName && isset($icons[$parentCategoryName])) {
            return $icons[$parentCategoryName];
        }

        return '🌱'; // Default icon for unknown categories
    }
}
