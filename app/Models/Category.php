<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
    ];
    

    public function index()
{
    $categories = Category::all(); 
    $products = Product::all();    

    return view('products.index', compact('categories', 'products'));
}
}
