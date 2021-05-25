<?php

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class InventoryTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGetAll()
    {
        $this->get('/inventory')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true]);
    }

    public function testGetSingleSuccess()
    {
        $inventory = Inventory::factory()->create();
        $this->get("/inventory/$inventory->id")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true, 'id' => $inventory->id]);
    }

    public function testGetSingleNotFound()
    {
        $this->get("/inventory/404")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson(['inventory' => null]);
    }

    public function testCreateSuccess()
    {
        $inventory = Inventory::factory()->make();
        $this->actingAs($this->adminStore())
            ->post("/inventory", $inventory->toArray())
            ->seeJson(['success' => true]);
        $this->seeInDatabase((new Inventory)->getTable(), ['stock' => $inventory->stock]);
    }

    public function testUpdate()
    {
        $inventory = Inventory::factory()->create();
        $stock = 21;
        $this->actingAs($this->adminStore())
            ->put("/inventory/$inventory->id", ['stock' => $stock])
            ->seeJson(['stock' => $stock]);
    }

    public function testDelete()
    {
        $inventory = Inventory::factory()->create();
        $this->actingAs($this->adminStore())
            ->delete("/inventory/$inventory->id")
            ->seeJson(['success' => true]);
        $this->notSeeInDatabase((new Inventory)->getTable(), ['stock' => $inventory->stock]);
    }

    public function adminStore()
    {
        return User::factory(['type' => 'admin_store'])->create();
    }
}
