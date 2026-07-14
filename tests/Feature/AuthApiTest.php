<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_register_with_password_at_eight_character_boundary(): void
    {
        $response = $this->postJson('/api/register', [
            'ime' => 'Test',
            'prezime' => 'Klijent',
            'email' => 'novi@salon.test',
            'telefon' => '0601234567',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'novi@salon.test')
            ->assertJsonPath('user.role', User::ROLE_KLIJENT)
            ->assertJsonStructure(['message', 'token', 'user' => ['klijent']]);

        $this->assertDatabaseHas('users', ['email' => 'novi@salon.test', 'role' => User::ROLE_KLIJENT]);
        $this->assertDatabaseHas('klijenti', ['ime' => 'Test', 'prezime' => 'Klijent']);
    }

    public function test_registration_rejects_password_below_eight_character_boundary(): void
    {
        $this->postJson('/api/register', [
            'ime' => 'Test',
            'prezime' => 'Klijent',
            'email' => 'kratka@salon.test',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $this->createUser('duplikat@salon.test');

        $this->postJson('/api/register', [
            'ime' => 'Drugi',
            'prezime' => 'Klijent',
            'email' => 'duplikat@salon.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_user_can_login_and_receive_new_token(): void
    {
        $user = $this->createUser('login@salon.test');

        $response = $this->postJson('/api/login', [
            'email' => 'login@salon.test',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['message', 'token', 'user']);

        $this->assertNotNull($user->fresh()->api_token);
    }

    public function test_login_rejects_wrong_password(): void
    {
        $this->createUser('login@salon.test');

        $this->postJson('/api/login', [
            'email' => 'login@salon.test',
            'password' => 'pogresna-lozinka',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_protected_endpoint_rejects_request_without_bearer_token(): void
    {
        $this->getJson('/api/user')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Token za autentifikaciju nije prosledjen.');
    }

    public function test_logout_invalidates_api_token(): void
    {
        $user = $this->createUser('odjava@salon.test', 'logout-token');

        $this->withToken('logout-token')
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertNull($user->fresh()->api_token);
    }

    private function createUser(string $email, ?string $token = null): User
    {
        return User::create([
            'name' => 'Test Klijent',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => User::ROLE_KLIJENT,
            'api_token' => $token,
        ]);
    }
}
