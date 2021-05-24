<?php

use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class ProductTypeTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGetAll()
    {
        $this->get('/product-type')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true]);
    }

    public function testGetSingleSuccess()
    {
        $productType = ProductType::factory()->create();
        $this->get("/product-type/$productType->id")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true, 'name' => $productType->name]);
    }

    public function testGetSingleNotFound()
    {
        $this->get("/product-type/404")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson(['product_type' => null]);
    }

    public function testCreateSuccess()
    {
        $productType = ProductType::factory()->make();
        $this->actingAs($this->adminRetail())
            ->post("/product-type", $productType->toArray())
            ->seeJson(['success' => true]);
        $this->seeInDatabase((new ProductType)->getTable(), ['name' => $productType->name]);
    }

    public function testUpdate()
    {
        $productType = ProductType::factory()->create();
        $name = 'product type 1';
        $this->actingAs($this->adminRetail())
            ->put("/product-type/$productType->id", ['name' => $name])
            ->seeJson(['name' => $name]);
    }

    public function testDelete()
    {
        $productType = ProductType::factory()->create();
        $this->actingAs($this->adminRetail())
            ->delete("/product-type/$productType->id")
            ->seeJson(['success' => true]);
        $this->notSeeInDatabase((new ProductType)->getTable(), ['name' => $productType->name]);
    }

    public function adminRetail()
    {
        return User::factory(['type' => 'admin_retail'])->create();
    }
}
