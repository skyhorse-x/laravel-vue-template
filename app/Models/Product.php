<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'luma_products';

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'specs' => 'array',
            'attributes' => 'array',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'status' => 'integer',
            'is_featured' => 'integer',
            'is_hot' => 'integer',
            'is_new' => 'integer',
            'stock' => 'integer',
            'sales' => 'integer',
            'sort' => 'integer',
            'category_id' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', 1);
    }
}
