<?php

namespace App\Models;

use App\Observers\OrderItemObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class OrderItem extends Model
{
    use HasFactory, OrderItemObserver;

    protected $fillable = [
        'order_id',
        'product_id',
        'amount',
        'at_time_price',
        'subtotal_price',
    ];

    public function scopeInfo($query)
    {
        return $query
            ->join('products', 'products.id', 'order_items.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->join('product_types', 'product_types.id', 'products.product_type_id')
            ->select([
                'order_items.*',
                'products.name',
                'product_types.name as product_type',
                'brands.name as brand_name',
                DB::raw("CONCAT('" . URL::asset('images/product') . "/', products.image) as product_image"),
            ]);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
