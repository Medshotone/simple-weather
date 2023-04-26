<?php

namespace Tests\Feature\API\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use Mockery;
use Tests\TestCase;

class ApiOAuthLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login_a_user_with_google()
    {
        $socialiteUser = $this->createMock(SocialUser::class);
        $socialiteUser->token = 'sdfsf';
        $socialiteUser->id = '1234567890';
        $socialiteUser->email = 'john@example.com';
        $socialiteUser->name = 'John Doe';

        Socialite::shouldReceive('driver->userFromToken')
            ->with('valid_token')
            ->andReturn($socialiteUser);

        $response = $this->postJson(route('api.login.google'), [
            'access_token' => 'valid_token',
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
                'name' => 'John Doe',
            ],
            'message' => 'User login successfully.',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertTrue(Hash::check('john@example.com', User::first()->password));
    }

    public function test_can_login_an_existing_user_with_google()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'provider_id' => null,
        ]);

        $socialiteUser = $this->createMock(SocialUser::class);
        $socialiteUser->token = 'sdfsf';
        $socialiteUser->id = '1234567890';
        $socialiteUser->email = 'john@example.com';
        $socialiteUser->name = 'John Doe';

        Socialite::shouldReceive('driver->userFromToken')
            ->with('valid_token')
            ->andReturn($socialiteUser);

        $response = $this->postJson(route('api.login.google'), [
            'access_token' => 'valid_token',
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
                'name' => 'John Doe',
            ],
            'message' => 'User login successfully.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }
}
