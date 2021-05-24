<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'barcode', 'brand_id', 'product_type_id', 'image'
    ];

    protected $appends = [
        'image_url'
    ];

    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getImageUrlAttribute()
    {
        return URL::asset("images/product/$this->image");
    }
}
