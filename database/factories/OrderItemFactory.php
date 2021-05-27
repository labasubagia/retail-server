<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $amount = $this->faker->numberBetween(1, 5);
        $price = $this->faker->numberBetween(1000, 10_000);
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'amount' => $amount,
            'at_time_price' => $price,
            'subtotal_price' => $amount * $price,
        ];
    }
}
