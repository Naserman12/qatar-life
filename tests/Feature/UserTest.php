<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase; // يعمل إعادة ضبط لقاعدة البيانات مع كل اختبار

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_be_created()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Naser Test',
            'email' => 'naser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'naser@example.com'
        ]);
    }
}
