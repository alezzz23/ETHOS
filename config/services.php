<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
        'map_id' => env('GOOGLE_MAPS_MAP_ID'),
    ],

    'ai_assistant' => [
        'api_key' => env('IA_API'),
        'base_url' => env('IA_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('IA_MODEL', 'mistralai/mistral-small-3.1-24b-instruct:free'),
        'timeout' => (int) env('IA_TIMEOUT', 30),
    ],

    'ai_dashboard' => [
        'api_key'  => env('IA_DASHBOARD_API'),
        'base_url' => env('IA_DASHBOARD_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model'    => env('IA_DASHBOARD_MODEL', 'nvidia/llama-nemotron-super-49b-v1:free'),
        'timeout'  => (int) env('IA_DASHBOARD_TIMEOUT', 30),
    ],

];
