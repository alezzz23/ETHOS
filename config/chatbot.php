<?php

/*
|--------------------------------------------------------------------------
| ETHOS Admin Chatbot — Centralized configuration
|--------------------------------------------------------------------------
| Usado por DashboardChatController y servicios relacionados.
| Evita magic numbers dispersos en código y permite tuning sin deploys.
*/

return [

    // ── LLM provider (OpenRouter-compatible) ────────────────────────
    'llm' => [
        'api_key'  => env('IA_DASHBOARD_API'),
        'base_url' => rtrim(env('IA_DASHBOARD_BASE_URL', 'https://integrate.api.nvidia.com/v1'), '/'),
        'timeout'  => (int) env('IA_DASHBOARD_TIMEOUT', 30),

        'models' => [
            'primary'  => env('IA_DASHBOARD_MODEL', 'nvidia/llama-nemotron-super-49b-v1:free'),
        ],

        'temperature' => (float) env('IA_DASHBOARD_TEMPERATURE', 0.55),
        'max_tokens'  => (int)   env('IA_DASHBOARD_MAX_TOKENS', 1200),
    ],

    // ── Conversation / message limits ───────────────────────────────
    'limits' => [
        'history_window'  => 20,   // mensajes enviados al LLM
        'history_storage' => 30,   // mensajes mantenidos en sesión
        'input_max_chars' => 2000,
        'rate_limit'      => '40,1',  // 40 req/min por usuario
        'feedback_rate'   => '20,1',
    ],

    // ── Retrieval Augmented Generation ──────────────────────────────
    'rag' => [
        'top_k'       => (int)  env('CHATBOT_KB_TOP_K', 4),
        'min_score'   => (int)  env('CHATBOT_KB_MIN_SCORE', 1),
        'summary_len' => 400,
    ],

    // ── Privacidad ──────────────────────────────────────────────────
    'privacy' => [
        // Si es false, NO se envían entries de la KB al LLM externo
        // (útil cuando KB contiene PII de clientes y el proveedor puede retener data).
        'allow_external_kb' => (bool) env('CHATBOT_ALLOW_EXTERNAL_KB', true),
    ],

    // ── Streaming (SSE) ─────────────────────────────────────────────
    'stream' => [
        'enabled' => (bool) env('CHATBOT_STREAMING', true),
    ],

    // ── Budgets (Fase 3) ────────────────────────────────────────────
    'budget' => [
        'enforce'         => (bool) env('CHATBOT_BUDGET_ENFORCE', true),
        'default_daily'   => (int)  env('CHATBOT_BUDGET_DAILY',   50_000),
        'default_monthly' => (int)  env('CHATBOT_BUDGET_MONTHLY', 1_000_000),
    ],

    // ── Tool calling (Fase 3) ───────────────────────────────────────
    'tools' => [
        'enabled'           => (bool) env('CHATBOT_TOOLS_ENABLED', true),
        'max_hops'          => (int)  env('CHATBOT_TOOLS_MAX_HOPS', 3),
        'allow_mutations'   => (bool) env('CHATBOT_TOOLS_ALLOW_MUTATIONS', true),
        'allow_destructive' => (bool) env('CHATBOT_TOOLS_ALLOW_DESTRUCTIVE', true),
    ],
];
