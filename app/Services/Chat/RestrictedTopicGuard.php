<?php

namespace App\Services\Chat;

use App\Models\RestrictedTopic;
use Illuminate\Support\Facades\Cache;

class RestrictedTopicGuard
{
    public function check(string $message): ?RestrictedTopic
    {
        $topics = Cache::remember(
            'admin_chatbot.restricted_topics',
            300,
            fn () => RestrictedTopic::active()->get()
        );

        return $topics->first(fn (RestrictedTopic $t): bool => $t->matches($message));
    }
}
