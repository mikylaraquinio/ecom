<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'parent_id',
    ];

    // 🔹 Parent Category Relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // 🔹 Subcategories Relationship (Recursive)
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('subcategories'); // Recursive
    }

    // 🔹 Check if category has subcategories
    public function hasSubcategories()
    {
        return $this->subcategories()->exists();
    }

    // 🔹 Scope to get only top-level categories
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    // 🔹 Check if it's a parent category (has children)
    public function isParentCategory()
    {
        return $this->subcategories()->exists();
    }
}
