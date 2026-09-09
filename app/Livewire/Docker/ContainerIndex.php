<?php

namespace App\Livewire\Docker;

use App\Models\Alert;
use App\Services\Contracts\DockerServiceInterface;
use App\Services\Monitoring\HostMetricsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Container')]
class ContainerIndex extends Component
{
    public array $containers = [];

    public bool $dockerAvailable = true;

    public string $filter = '';

    public bool $showLogs = false;

    public string $selectedContainer = '';

    public int $pollInterval = 15000;

    public function mount(DockerServiceInterface $docker, HostMetricsService $hostMetrics)
    {
        $this->refresh($docker, $hostMetrics);
    }

    public function refresh(DockerServiceInterface $docker, HostMetricsService $hostMetrics)
    {
        $this->dockerAvailable = $docker->ping();

        $this->containers = $this->dockerAvailable ? $docker->listContainers(true) : [];
    }

    public function startContainer(DockerServiceInterface $docker, string $id)
    {
        $docker->startContainer($id);
        $this->refresh($docker, app(HostMetricsService::class));
    }

    public function stopContainer(DockerServiceInterface $docker, string $id)
    {
        $docker->stopContainer($id);
        $this->refresh($docker, app(HostMetricsService::class));
    }

    public function restartContainer(DockerServiceInterface $docker, string $id)
    {
        $docker->restartContainer($id);
        $this->refresh($docker, app(HostMetricsService::class));
    }

    public function toggleLogs(string $id)
    {
        $this->showLogs = true;
        $this->selectedContainer = $id;
    }

    public function closeLogs()
    {
        $this->showLogs = false;
        $this->selectedContainer = '';
    }

    public function render(DockerServiceInterface $docker, HostMetricsService $hostMetrics)
    {
        $filtered = $this->filter ? collect($this->containers)->filter(fn ($c) => stripos(implode(' ', $c), $this->filter) !== false)->values()->all() : $this->containers;

        $running = $filtered ? collect($filtered)->filter(fn ($c) => ($c['State'] ?? '') === 'running')->count() : 0;

        return view('livewire.container-index', [
            'containers' => $filtered,
            'running' => $running,
            'dockerAvailable' => $this->dockerAvailable,
            'alerts' => Alert::where('status', 'firing')->latest()->limit(3)->get(),
            'filter' => $this->filter,
        ]);
    }
}
