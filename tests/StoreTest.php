<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class StoreTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGetAll()
    {
        $this->get('/store')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true]);
    }

    public function testGetSingleSuccess()
    {
        $store = Store::factory()->create();
        $this->get("store/$store->id")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true, 'name' => $store->name]);
    }

    public function testGetSingleNotFound()
    {
        $this->get("store/404")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson(['store' => null]);
    }

    public function testCreateSuccess()
    {
        $store = Store::factory()->make();
        $this->actingAs($this->adminRetail())
            ->post("store", $store->toArray())
            ->seeJson(['success' => true]);
        $this->seeInDatabase((new Store)->getTable(), ['name' => $store->name]);
    }

    public function testUpdate()
    {
        $store = Store::factory()->create();
        $name = 'store 1';
        $this->actingAs($this->adminRetail())
            ->put("store/$store->id", ['name' => $name])
            ->seeJson(['name' => $name]);
    }

    public function testDelete()
    {
        $store = Store::factory()->create();
        $this->actingAs($this->adminRetail())
            ->delete("store/$store->id")
            ->seeJson(['success' => true]);
        $this->notSeeInDatabase((new Store)->getTable(), ['name' => $store->name]);
    }

    public function adminRetail()
    {
        return User::factory(['type' => 'admin_retail'])->create();
    }
}
