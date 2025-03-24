<?php

if (!function_exists('getCategoryIcon')) {
    function getCategoryIcon($categoryName, $parentCategoryName = null, $grandparentCategoryName = null)
    {
        $icons = [
            'Grains & Cereals' => '🌾',
            'Vegetables' => '🥦',
            'Fruits' => '🍎',
            'Herbs & Spices' => '🌿',
            'Livestock' => '🐄',
            'Poultry' => '🐔'
        ];

        // 1️⃣ Check if the category has its own icon
        if (isset($icons[$categoryName])) {
            return $icons[$categoryName];
        }

        // 2️⃣ If not, inherit from its direct parent
        if ($parentCategoryName && isset($icons[$parentCategoryName])) {
            return $icons[$parentCategoryName];
        }

        // 3️⃣ If still no icon, inherit from grandparent (main category)
        if ($grandparentCategoryName && isset($icons[$grandparentCategoryName])) {
            return $icons[$grandparentCategoryName];
        }

        return '🌱'; // Default icon for unknown categories
    }
}
