<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_screen_can_be_rendered_after_authenticate()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

        $this->get(route('home'))
            ->assertStatus(200);
    }

    public function test_home_screen_can_not_be_rendered_without_authenticate()
    {
        $this->get(route('home'))
            ->assertRedirect('/login');
    }
}
