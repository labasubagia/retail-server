<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'barcode', 'brand_id', 'product_type_id', 'image'
    ];

    public function scopeInfo($query)
    {
        $selected = [
            'products.*',
            'product_types.name as type',
            'brands.name as brand',
        ];
        if (env('APP_ENV') != 'testing') {
            array_push(
                $selected,
                DB::raw("CONCAT('" . URL::asset('images/product') . "/', products.image) as image_url")
            );
        }
        return $query
            ->join('product_types', 'product_types.id', 'products.product_type_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->select($selected);
    }

    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
