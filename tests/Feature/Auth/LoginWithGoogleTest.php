<?php

namespace Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialUser;

class LoginWithGoogleTest extends TestCase
{
    use RefreshDatabase;

    public function mockSocialiteFacade($email = 'foo@bar.com', $token = 'foo', $id = 1)
    {
        $socialiteUser = $this->createMock(SocialUser::class);
        $socialiteUser->token = $token;
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;

        $provider = $this->createMock(GoogleProvider::class);
        $provider->expects($this->any())
            ->method('user')
            ->willReturn($socialiteUser);

        $stub = $this->createMock(Socialite::class);
        $stub->expects($this->any())
            ->method('driver')
            ->willReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }

    public function test_redirects_to_google()
    {
        $response = $this->get(route('login.google'));

        $this->assertStringContainsString(
            'accounts.google.com/o/oauth2/auth?client_id=' . env('GOOGLE_CLIENT_ID'),
            $response->getTargetUrl()
        );
    }

    public function test_retrieves_google_request_and_creates_a_new_user()
    {
        $user = User::factory()->create();

        // Mock the Facade and return a User Object with the email 'foo@bar.com'
        $this->mockSocialiteFacade($user->email);

        $this->get(route('login.google.callback'))
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertTrue(User::where('email', $user->email)->exists());
    }
}
