<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // === Main Categories ===
            $categories = [
                'Grains & Cereals' => [
                    'Cereal Grains', 
                    'Pseudocereal Grains'
                ],
                'Vegetables' => [
                    'Brassicas (Cabbage Family)',
                    'Fruiting Vegetables',
                    'Leafy Greens',
                    'Legumes',
                    'Mushrooms & Fungi',
                    'Other & Specialty Vegetables',
                    'Roots & Tubers',
                ],
                'Fruits' => [
                    'Citrus Fruits',
                    'Berries',
                    'Melons',
                    'Tropical & Exotic Fruits',
                    'Stone Fruits (Drupes)',
                    'Pome Fruits',
                    'Figs & Related Fruits',
                    'Vine Fruits',
                    'Nutritious & Super Fruits',
                    'Unique/Other Fruits',
                ],
                'Herbs & Spices' => [
                    'Seeds & Pods',
                    'Roots, Rhizomes, & Bark',
                    'Fruits, Berries & Peppers',
                    'Floral & Other Exotic Spices',
                    'Leafy & Aromatic Herbs',
                    'Bay Leaves',
                    'Floral & Unique Herbs',
                    'Medicinal & Traditional Herbs',
                    'Asian & African Specialty Herbs',
                    'Other Specialty Herbs',
                ],
            ];

            foreach ($categories as $mainCategory => $subcategories) {
                // Insert main category and get ID
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $mainCategory,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Prepare subcategories data
                $subcategoryData = array_map(fn($sub) => [
                    'name' => $sub,
                    'parent_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $subcategories);

                // Insert subcategories
                DB::table('categories')->insert($subcategoryData);
            }
        });
    }
}
