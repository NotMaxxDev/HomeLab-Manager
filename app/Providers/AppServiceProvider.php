<?php

namespace App\Providers;

use App\Services\Agent\AgentService;
use App\Services\Agent\ToolRegistry;
use App\Services\Contracts\DockerServiceInterface;
use App\Services\Docker\DockerService;
use App\Services\Llm\LlmService;
use App\Services\Monitoring\HostMetricsService;
use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, fn () => new SettingsService);
        $this->app->singleton(HostMetricsService::class, fn () => new HostMetricsService);
        $this->app->singleton(LlmService::class, fn () => new LlmService);

        $this->app->bind(DockerServiceInterface::class, DockerService::class);

        $this->app->singleton(ToolRegistry::class, fn ($app) => new ToolRegistry(
            $app->make(DockerServiceInterface::class),
            $app->make(HostMetricsService::class),
        ));

        $this->app->singleton(AgentService::class, fn ($app) => new AgentService(
            $app->make(LlmService::class),
            $app->make(ToolRegistry::class),
            $app->make(SettingsService::class),
            $app->make(\App\Services\AuditService::class),
        ));
    }

    public function boot(): void
    {
        //
    }
}
