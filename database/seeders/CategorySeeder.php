<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Add this line

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'Fresh Produce']);
        Category::create(['name' => 'Dairy Products']);
        Category::create(['name' => 'Grains and Pulses']);
    }
}
