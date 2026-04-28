<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AdminChatLog;
use App\Models\ChatFeedback;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class DashboardChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        config()->set('chatbot.llm.api_key', 'test-key');
        config()->set('chatbot.tools.enabled', false); // aislamos tool-calling en otro test
    }

    private function adminUser(): User
    {
        /** @var User $u */
        $u = User::query()->where('email', 'admin@ethos.com')->firstOrFail();
        $u->forceFill(['email_verified_at' => now()])->save();
        return $u;
    }

    public function test_chat_returns_reply_when_upstream_ok(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [['message' => ['content' => 'Hola desde la IA.'], 'finish_reason' => 'stop']],
                'usage'   => ['total_tokens' => 42],
            ], 200),
        ]);

        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat'), [
                'message' => '¿Cuál es el estado general?',
                'history' => [],
            ]);

        $res->assertOk()
            ->assertJsonStructure(['reply', 'tokens_used', 'conversation_id', 'admin_chat_log_id']);

        $this->assertDatabaseCount('admin_chat_logs', 2); // user + assistant
    }

    public function test_chat_returns_503_on_upstream_error(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat'), [
                'message' => 'hola',
                'history' => [],
            ]);

        $res->assertStatus(500);
    }

    public function test_chat_retries_without_tools_when_upstream_rejects_tool_use(): void
    {
        config()->set('chatbot.tools.enabled', true);

        Http::fakeSequence()
            ->push([
                'error' => [
                    'message' => 'No endpoints found that support tool use. Try disabling "get_project_status".',
                    'code' => 404,
                ],
            ], 404)
            ->push([
                'choices' => [['message' => ['content' => 'Respuesta sin tools.'], 'finish_reason' => 'stop']],
                'usage' => ['total_tokens' => 24],
            ], 200);

        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat'), [
                'message' => 'hola',
                'history' => [],
            ]);

        $res->assertOk()->assertJson(['reply' => 'Respuesta sin tools.']);
        Http::assertSentCount(2);

        $requests = Http::recorded()->map(fn (array $record): HttpRequest => $record[0])->values();

        $this->assertArrayHasKey('tools', $requests[0]->data());
        $this->assertArrayNotHasKey('tools', $requests[1]->data());
        $this->assertArrayNotHasKey('tool_choice', $requests[1]->data());
    }

    public function test_chat_validates_empty_message(): void
    {
        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat'), [
                'message' => '',
                'history' => [],
            ]);

        $res->assertStatus(422);
    }

    public function test_feedback_persists_row(): void
    {
        $log = AdminChatLog::create([
            'user_id'     => $this->adminUser()->id,
            'session_id'  => 'sess-1',
            'role'        => 'assistant',
            'content'     => 'respuesta',
            'model'       => 'fake',
            'tokens_used' => 10,
            'response_ms' => 100,
        ]);

        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat.feedback'), [
                'admin_chat_log_id'  => $log->id,
                'rating'             => 'helpful',
                'context'            => 'dashboard',
                'user_message'       => 'ping',
                'assistant_message'  => 'pong',
            ]);

        $res->assertOk();
        $this->assertDatabaseCount('chat_feedback', 1);
        $this->assertSame('helpful', ChatFeedback::first()->rating);
    }
}
