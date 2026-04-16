<?php

namespace App\Http\Middleware;

use App\Models\UserAiBudget;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fase 3.17 — Verifica que el usuario no haya excedido su cuota de tokens IA.
 * Responde 429 con retry-after cuando se excede el tope diario/mensual.
 */
class CheckAiBudget
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('chatbot.budget.enforce', true)) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user) return $next($request);

        $caps  = UserAiBudget::effectiveCaps($user->id);
        $usage = UserAiBudget::usage($user->id);

        if ($caps['daily'] > 0 && $usage['daily'] >= $caps['daily']) {
            return $this->tooMany('daily', $caps['daily'], $usage['daily'], now()->endOfDay()->diffInSeconds(now()));
        }
        if ($caps['monthly'] > 0 && $usage['monthly'] >= $caps['monthly']) {
            return $this->tooMany('monthly', $caps['monthly'], $usage['monthly'], now()->endOfMonth()->diffInSeconds(now()));
        }

        return $next($request);
    }

    private function tooMany(string $scope, int $cap, int $used, int $retryAfterSeconds): Response
    {
        return response()->json([
            'message'     => 'Has alcanzado tu cuota de uso de IA (' . $scope . ').',
            'scope'       => $scope,
            'cap'         => $cap,
            'used'        => $used,
            'retry_after' => $retryAfterSeconds,
        ], 429)->header('Retry-After', (string) max(60, $retryAfterSeconds));
    }
}
