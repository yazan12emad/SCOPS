<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_activity_log_for_new_user(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'activityuser',
            'email' => 'activity@example.com',
            'password' => 'password123',
            'phone' => '0791234567',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('activity_logs', [
            'actor_type' => 'system',
            'action_type' => 'created',
            'entity_type' => 'User',
        ]);

        $this->assertSame(1, ActivityLog::query()->count());
    }
}
