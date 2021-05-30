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
        $request = $this->actingAs($this->adminRetail())
            ->call(
                "POST",
                "/product",
                $product->toArray(),
                [],
                ['image' => UploadedFile::fake()->image('file.png', 600, 600)]
            );
        $content = json_decode($request->baseResponse->getContent());
        $this->assertEquals(Response::HTTP_CREATED, $request->baseResponse->getStatusCode());
        $this->assertTrue($content->success);
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
