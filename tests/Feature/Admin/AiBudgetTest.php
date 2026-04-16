<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AdminChatLog;
use App\Models\User;
use App\Models\UserAiBudget;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class AiBudgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        config()->set('chatbot.llm.api_key', 'test-key');
        config()->set('chatbot.tools.enabled', false);
    }

    private function adminUser(): User
    {
        /** @var User $u */
        $u = User::query()->where('email', 'admin@ethos.com')->firstOrFail();
        $u->forceFill(['email_verified_at' => now()])->save();
        return $u;
    }

    public function test_user_under_cap_can_chat(): void
    {
        Http::fake(['*' => Http::response([
            'choices' => [['message' => ['content' => 'ok'], 'finish_reason' => 'stop']],
            'usage'   => ['total_tokens' => 10],
        ], 200)]);

        $user = $this->adminUser();
        UserAiBudget::create(['user_id' => $user->id, 'daily_token_cap' => 1000, 'monthly_token_cap' => 100000]);

        $res = $this->actingAs($user)->postJson(route('admin.chat'), ['message' => 'hola', 'history' => []]);
        $res->assertOk();
    }

    public function test_user_over_daily_cap_gets_429(): void
    {
        $user = $this->adminUser();
        UserAiBudget::create(['user_id' => $user->id, 'daily_token_cap' => 100, 'monthly_token_cap' => 100000]);

        // Consumo previo que excede el cap
        AdminChatLog::create([
            'user_id' => $user->id, 'session_id' => 's1', 'role' => 'assistant',
            'content' => 'prev', 'model' => 'x', 'tokens_used' => 150, 'response_ms' => 10,
        ]);

        $res = $this->actingAs($user)->postJson(route('admin.chat'), ['message' => 'hola', 'history' => []]);
        $res->assertStatus(429)
            ->assertJsonStructure(['message', 'scope', 'cap', 'used', 'retry_after']);
    }
}
