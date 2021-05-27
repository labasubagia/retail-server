<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'store_id' => Store::factory(),
            'customer_id' => User::factory(),
            'created_by' => User::factory(),
            'status' => $this->faker->randomElement('finished', 'wait_delivery', 'cancelled'),
            'total_price' => $this->faker->numberBetween(10_000, 20_000),
        ];
    }
}
