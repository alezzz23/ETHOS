<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Service;
use App\Observers\ProjectObserver;
use App\Observers\ServiceObserver;
use App\Services\Chat\ChatFormFactory;
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
        $this->app->singleton(ChatFormFactory::class);

        // Fase 3 — Registro singleton de ToolRegistry con las tools de chat.
        $this->app->singleton(\App\Services\Chat\Tools\ToolRegistry::class, function () {
            $r = new \App\Services\Chat\Tools\ToolRegistry();
            $r->register(new \App\Services\Chat\Tools\GetProjectStatusTool());
            $r->register(new \App\Services\Chat\Tools\ListPendingTasksTool());
            $r->register(new \App\Services\Chat\Tools\SearchClientsTool());
            $r->register(new \App\Services\Chat\Tools\SearchUsersTool());
            $r->register(new \App\Services\Chat\Tools\GetProposalTool());
            $r->register(new \App\Services\Chat\Tools\GetMetricsTool());

            $r->register(new \App\Services\Chat\Tools\OpenCreationFormTool('user', 'users.manage', 'usuario'));
            $r->register(new \App\Services\Chat\Tools\OpenCreationFormTool('client', 'clients.create', 'cliente'));
            $r->register(new \App\Services\Chat\Tools\OpenCreationFormTool('project', 'projects.create', 'proyecto'));
            $r->register(new \App\Services\Chat\Tools\OpenCreationFormTool('service', 'services.create', 'servicio'));

            // CRUD directo via tools (usuarios/clientes)
            $r->register(new \App\Services\Chat\Tools\CreateUserTool());
            $r->register(new \App\Services\Chat\Tools\UpdateUserTool());
            $r->register(new \App\Services\Chat\Tools\DeleteUserTool());
            $r->register(new \App\Services\Chat\Tools\CreateClientTool());
            $r->register(new \App\Services\Chat\Tools\UpdateClientTool());
            $r->register(new \App\Services\Chat\Tools\DeleteClientTool());
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
        Password::defaults(fn () => Password::min(8));

        // Only force HTTPS when the deployment is actually configured for it.
        if ($this->app->isProduction() && parse_url((string) config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }
    }
}

