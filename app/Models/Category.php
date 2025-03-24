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

    // Parent Category Relationship (reverse of children)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Children Categories Relationship
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Optionally, add a method to easily get subcategories
    public function subcategories()
    {
        return $this->children();  // Alias for children() method
    }

    // Check if the category has subcategories
    public function hasSubcategories()
    {
        return $this->children()->exists();
    }
}
