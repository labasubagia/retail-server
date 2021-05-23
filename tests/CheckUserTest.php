<?php

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CheckUserTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testIsAdminRetail()
    {
        $url = '/check/is-admin-retail';
        $this->actingAs($this->adminRetail())->get($url)->seeStatusCode(Response::HTTP_OK);
        $this->actingAs($this->adminStore())->get($url)->seeStatusCode(Response::HTTP_FORBIDDEN);
        $this->actingAs($this->customer())->get($url)->seeStatusCode(Response::HTTP_FORBIDDEN);
    }

    public function testIsAdminStore()
    {
        $url = '/check/is-admin-store';
        $this->actingAs($this->adminStore())->get($url)->seeStatusCode(Response::HTTP_OK);
        $this->actingAs($this->adminRetail())->get($url)->seeStatusCode(Response::HTTP_FORBIDDEN);
        $this->actingAs($this->customer())->get($url)->seeStatusCode(Response::HTTP_FORBIDDEN);
    }

    public function testIsCustomer()
    {
        $url = '/check/is-customer';
        $this->actingAs($this->customer())->get($url)->seeStatusCode(Response::HTTP_OK);
        $this->actingAs($this->adminRetail())->get($url)->seeStatusCode(Response::HTTP_FORBIDDEN);
        $this->actingAs($this->adminStore())->get($url)->seeStatusCode(Response::HTTP_FORBIDDEN);
    }

    private function adminRetail()
    {
        return User::factory(['type' => 'admin_retail'])->create();
    }

    private function adminStore()
    {
        return User::factory(['type' => 'admin_store'])->create();
    }

    private function customer()
    {
        return User::factory(['type' => 'customer'])->create();
    }
}
