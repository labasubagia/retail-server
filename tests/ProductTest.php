<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class ProductTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGetAll()
    {
        $this->get('/product')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true]);
    }

    public function testGetSingleSuccess()
    {
        $product = Product::factory()->create();
        $this->get("/product/$product->id")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true, 'name' => $product->name]);
    }

    public function testGetSingleNotFound()
    {
        $this->get("/product/404")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson(['product' => null]);
    }

    public function testCreateSuccess()
    {
        $product = Product::factory()->make();
        $this->actingAs($this->adminRetail())
            ->post("/product", array_merge(
                $product->toArray(),
                ['image' => UploadedFile::fake()->image('file.png', 600, 600)]
            ))
            ->seeJson(['success' => true]);
        $this->seeInDatabase((new Product)->getTable(), ['name' => $product->name]);
    }

    public function testUpdate()
    {
        $product = Product::factory()->create();
        $name = 'product 1';
        $this->actingAs($this->adminRetail())
            ->post("/product/$product->id", ['name' => $name])
            ->seeJson(['name' => $name]);
    }

    public function testDelete()
    {
        $product = Product::factory()->create();
        $this->actingAs($this->adminRetail())
            ->delete("/product/$product->id")
            ->seeJson(['success' => true]);
        $this->notSeeInDatabase((new Product)->getTable(), ['name' => $product->name]);
    }

    public function adminRetail()
    {
        return User::factory(['type' => 'admin_retail'])->create();
    }
}
