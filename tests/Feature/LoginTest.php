<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_can_log_in(): void
    {
        $user = User::create([
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '0791234567',
        ]);

        $response = $this->postJson('/api/logIn', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Logged in successfully.',
                'user' => [
                    'user_id' => $user->user_id,
                    'email' => 'test@example.com',
                ],
            ]);

        $this->assertSame(1, $user->fresh()->tokens()->count());
    }

    public function test_authenticated_user_cannot_log_in_again(): void
    {
        $user = User::create([
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '0791234567',
        ]);

        $existingToken = $user->createToken('existing_token')->plainTextToken;

        $response = $this
            ->withToken($existingToken)
            ->postJson('/api/logIn', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'User is already logged in.',
            ]);

        $this->assertSame(1, $user->fresh()->tokens()->count());
    }
}
