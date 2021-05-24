<?php

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;


class VendorTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGetAll()
    {
        $this->get('/vendor')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true]);
    }

    public function testGetSingleSuccess()
    {
        $vendor = Vendor::factory()->create();
        $this->get("vendor/$vendor->id")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(['success' => true, 'name' => $vendor->name]);
    }

    public function testGetSingleNotFound()
    {
        $this->get("vendor/404")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson(['vendor' => null]);
    }

    public function testCreateSuccess()
    {
        $vendor = Vendor::factory()->make();
        $this->actingAs($this->adminRetail())
            ->post("vendor", $vendor->toArray())
            ->seeJson(['success' => true]);
        $this->seeInDatabase((new Vendor)->getTable(), ['name' => $vendor->name]);
    }

    public function testUpdate()
    {
        $vendor = Vendor::factory()->create();
        $name = 'vendor 1';
        $this->actingAs($this->adminRetail())
            ->put("vendor/$vendor->id", ['name' => $name])
            ->seeJson(['name' => $name]);
    }

    public function testDelete()
    {
        $vendor = Vendor::factory()->create();
        $this->actingAs($this->adminRetail())
            ->delete("vendor/$vendor->id")
            ->seeJson(['success' => true]);
        $this->notSeeInDatabase((new Vendor)->getTable(), ['name' => $vendor->name]);
    }

    public function adminRetail()
    {
        return User::factory(['type' => 'admin_retail'])->create();
    }
}
