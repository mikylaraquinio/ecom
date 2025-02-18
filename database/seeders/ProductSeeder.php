<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $freshProduce = Category::where('name', 'Fresh Produce')->first();
        $dairyProducts = Category::where('name', 'Dairy Products')->first();
        $grainsAndPulses = Category::where('name', 'Grains and Pulses')->first();

        Product::create([
            'name' => 'Apple',
            'description' => 'Fresh apples from the farm.',
            'price' => 19.99,
            'image' => 'apple.jpg',
            'category_id' => $freshProduce->id,
        ]);

        Product::create([
            'name' => 'Milk',
            'description' => 'Fresh dairy milk.',
            'price' => 2.99,
            'image' => 'milk.jpg',
            'category_id' => $dairyProducts->id,
        ]);

        Product::create([
            'name' => 'Rice',
            'description' => 'High-quality rice.',
            'price' => 10.99,
            'image' => 'rice.jpg',
            'category_id' => $grainsAndPulses->id,
        ]);
    }
}
