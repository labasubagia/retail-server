<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'barcode' => $this->faker->isbn10(),
            'product_type_id' => ProductType::factory(),
            'brand_id' => Brand::factory(),
        ];
    }
}
