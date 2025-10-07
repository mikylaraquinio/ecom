<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id','path','sort_order'];

    protected $touches = ['product'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function url(): string {
        return asset('storage/'.$this->path);
    }
}

