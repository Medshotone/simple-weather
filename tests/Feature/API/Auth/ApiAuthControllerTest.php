<?php

namespace Tests\Feature\API\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login_a_user()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret1234'),
        ]);

        $response = $this->postJson(route('api.login'), [
            'email' => 'john@example.com',
            'password' => 'secret1234',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'name',
            ],
            'message',
        ]);
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => $user->name,
            ],
            'message' => 'User signed in',
        ]);
    }

    public function test_returns_error_if_login_fails()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret1234'),
        ]);

        $response = $this->postJson(route('api.login'), [
            'email' => 'john@example.com',
            'password' => '123secret1234',
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'error',
            ],
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorised.',
            'data' => [
                'error' => 'Unauthorised',
            ],
        ]);
    }

    public function test_returns_error_if_login_validation_fails()
    {
        $response = $this->postJson(route('api.login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'email',
                'password',
            ],
        ]);
        $response->assertJson([
            'success' => false,
            'message' => 'Error validation',
        ]);
    }

    public function test_can_register_a_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret1234!',
            'password_confirmation' => 'secret1234!',
        ];

        $response = $this->postJson(route('api.register'), $userData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'name',
            ],
            'message',
        ]);
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => $userData['name'],
            ],
            'message' => "User created successfully.",
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $this->assertTrue(Hash::check($userData['password'], User::first()->password));
    }
}
