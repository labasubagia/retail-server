<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'price', 'stock', 'product_id', 'store_id', 'vendor_id'
    ];

    public function scopeInfo($query)
    {
        return $query
            ->join('stores', 'stores.id', 'inventories.store_id')
            ->join('vendors', 'vendors.id', 'inventories.vendor_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->join('product_types', 'product_types.id', 'products.product_type_id')
            ->select([
                'inventories.*',
                'stores.name as store_name',
                'vendors.name as vendor_name',
                'product_types.name as product_type',
                'brands.name as brand_name',
                'products.name as product_name',
                'products.barcode as product_barcode',
                'products.image as product_image'
            ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
