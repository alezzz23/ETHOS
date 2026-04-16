<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Service;
use App\Observers\ProjectObserver;
use App\Observers\ServiceObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fase 3 — Registro singleton de ToolRegistry con las tools de chat.
        $this->app->singleton(\App\Services\Chat\Tools\ToolRegistry::class, function () {
            $r = new \App\Services\Chat\Tools\ToolRegistry();
            $r->register(new \App\Services\Chat\Tools\GetProjectStatusTool());
            $r->register(new \App\Services\Chat\Tools\ListPendingTasksTool());
            $r->register(new \App\Services\Chat\Tools\SearchClientsTool());
            $r->register(new \App\Services\Chat\Tools\GetProposalTool());
            $r->register(new \App\Services\Chat\Tools\GetMetricsTool());
            return $r;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Service::observe(ServiceObserver::class);
        Project::observe(ProjectObserver::class);

        // Baseline password policy for every Password::defaults() usage.
        Password::defaults(function () {
            $rule = Password::min(10)
                ->mixedCase()
                ->numbers()
                ->symbols();

            return $this->app->isProduction()
                ? $rule->uncompromised()
                : $rule;
        });

        // Force HTTPS in production to avoid mixed content and token leakage.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}

