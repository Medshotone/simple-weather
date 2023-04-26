<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_can_get_the_current_user()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $currentUser = $this->userRepository->getCurrentUser();

        $this->assertEquals($user->toArray(), $currentUser);
    }

    public function test_can_create_a_user()
    {
        Event::fake();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret',
        ];

        $user = $this->userRepository->createUser($userData);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $this->assertTrue(Hash::check($userData['password'], $user->password));

        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_can_create_a_user_with_provider_id()
    {
        Event::fake();

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'id' => '1234567890',
        ];

        $user = $this->userRepository->createUser($userData);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'provider_id' => $user->provider_id,
            'password' => $user->password,
        ]);

        $this->assertTrue(Hash::check($userData['email'], $user->password));

        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_can_get_a_user_by_email()
    {
        $user = User::factory()->create();

        $foundUser = $this->userRepository->getUserByEmail($user->email);

        $this->assertEquals($user->id, $foundUser->id);
    }

    public function test_returns_null_if_no_user_found_by_email()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $foundUser = $this->userRepository->getUserByEmail('jane@example.com');

        $this->assertNull($foundUser);
    }
}
