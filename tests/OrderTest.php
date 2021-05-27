<?php

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class OrderTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;


    public function testCreate()
    {
        $store = Store::factory()->create();
        $user = $this->adminStore(['store_id' => $store->id]);

        $product1 = Product::factory()->create();
        $inventory1 = Inventory::factory([
            'product_id' => $product1->id,
            'store_id' => $store->id,
            'stock' => 10,
        ])->create();

        $product2 = Product::factory()->create();
        $inventory2 = Inventory::factory([
            'product_id' => $product2->id,
            'store_id' => $store->id,
            'stock' => 20,
        ])->create();

        $this->actingAs($user)
            ->post('/order', [
                'store_id' => $store->id,
                'products' => [
                    ['id' => $product1->id, 'amount' => 5],
                    ['id' => $product2->id, 'amount' => 10],
                ]
            ])
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson(['success' => true]);

        $this->seeInDatabase(
            (new Inventory)->getTable(),
            ['id' => $inventory1->id, 'stock' => 5]
        );
        $this->seeInDatabase(
            (new Inventory)->getTable(),
            ['id' => $inventory2->id, 'stock' => 10]
        );
    }

    public function adminStore($payload)
    {
        $payload = array_merge($payload, ['type' => 'admin_store']);
        return User::factory($payload)->create();
    }
}
