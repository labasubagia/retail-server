<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'barcode', 'brand_id', 'product_type_id', 'image'
    ];

    public function scopeInfo($query)
    {
        return $query
            ->join('product_types', 'product_types.id', 'products.product_type_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->select([
                'products.*',
                'product_types.name as type',
                'brands.name as brand',
                'products.image as image_url'
            ]);
    }

    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
