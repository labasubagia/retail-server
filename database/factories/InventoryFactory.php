<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition()
    {
        return [
            'price' => $this->faker->numberBetween(1000, 2000),
            'stock' => $this->faker->numberBetween(5, 30),
            'product_id' => Product::factory(),
            'store_id' => Store::factory(),
            'vendor_id' => Vendor::factory(),
        ];
    }
}
