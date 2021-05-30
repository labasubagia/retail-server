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
        'inventory_id',
        'amount',
        'at_time_price',
        'subtotal_price',
    ];

    public function scopeInfo($query)
    {
        $selected = [
            'order_items.*',
            'products.name',
            'product_types.name as product_type',
            'brands.name as brand_name',
        ];
        if (env('APP_ENV') != 'testing') {
            array_push(
                $selected,
                DB::raw("CONCAT('" . URL::asset('images/product') . "/', products.image) as product_image"),
            );
        }
        return $query
            ->join('inventories', 'inventories.id', 'order_items.inventory_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->join('product_types', 'product_types.id', 'products.product_type_id')
            ->select($selected);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
