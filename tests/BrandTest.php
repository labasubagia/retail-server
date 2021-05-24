<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class BrandTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGetAll()
    {
        $this->get('/brand')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true]);
    }

    public function testGetSingleSuccess()
    {
        $brand = Brand::factory()->create();
        $this->get("/brand/$brand->id")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true, 'name' => $brand->name]);
    }

    public function testGetSingleNotFound()
    {
        $this->get("/brand/404")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson(['brand' => null]);
    }

    public function testCreateSuccess()
    {
        $brand = Brand::factory()->make();
        $this->actingAs($this->adminRetail())
            ->post("/brand", $brand->toArray())
            ->seeJson(['success' => true]);
        $this->seeInDatabase((new Brand)->getTable(), ['name' => $brand->name]);
    }

    public function testUpdate()
    {
        $brand = Brand::factory()->create();
        $name = 'brand 1';
        $this->actingAs($this->adminRetail())
            ->put("/brand/$brand->id", ['name' => $name])
            ->seeJson(['name' => $name]);
    }

    public function testDelete()
    {
        $brand = Brand::factory()->create();
        $this->actingAs($this->adminRetail())
            ->delete("/brand/$brand->id")
            ->seeJson(['success' => true]);
        $this->notSeeInDatabase((new Brand)->getTable(), ['name' => $brand->name]);
    }

    public function adminRetail()
    {
        return User::factory(['type' => 'admin_retail'])->create();
    }
}
