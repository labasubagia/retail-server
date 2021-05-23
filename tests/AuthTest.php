<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testRegisterSuccess()
    {
        $user = User::factory()->make();
        $payload = array_merge($user->toArray(), ['password' => '12345678']);
        $this->post('/auth/register', $payload)->seeJson(['success' => true]);
    }

    public function testLoginSuccess()
    {
        $user = User::factory()->create();
        $payload = ['email' => $user->email, 'password' => '12345678'];
        $this->post('/auth/login', $payload)->seeJson(['success' => true]);
    }

    public function testLoginFailed()
    {
        $user = User::factory()->create();
        $payload = ['email' => $user->email, 'password' => '8765321'];
        $this->post('/auth/login', $payload)->seeJson(['success' => false]);
    }

    public function testCurrentUserSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/auth/current')
            ->seeJson(['api_token' => $user->api_token]);
    }
}
