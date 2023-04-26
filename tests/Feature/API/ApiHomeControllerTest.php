<?php

namespace Tests\Feature\API;

use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiHomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_can_get_the_user_and_weather_data()
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

        $response = $this->withToken(json_decode($response->getContent(), true)['data']['token'])
            ->getJson('/api/home');

        $response->assertJsonStructure([
            'userData' => [
                'id',
                'name',
                'email',
                'lat',
                'lon',
            ],
            'weatherData' => [
                'temp',
                'humidity',
            ],
        ]);
        $response->assertJson([
            'userData' => [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ],
        ]);
    }

    public function test_returns_error_if_not_authenticated()
    {
        $response = $this->getJson('/api/home');

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
        ]);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
