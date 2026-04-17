<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Railway (and most PaaS edges) forward requests through their own
     * reverse proxy. We trust them via TRUSTED_PROXIES env (default: '*'
     * in production) so that the correct scheme/host is reported to Laravel.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    public function __construct()
    {
        $configured = env('TRUSTED_PROXIES');

        if ($configured !== null && $configured !== '') {
            $this->proxies = $configured === '*'
                ? '*'
                : array_values(array_filter(array_map('trim', explode(',', $configured))));
        }
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
