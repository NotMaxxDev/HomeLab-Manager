<?php

namespace App\Livewire;

use App\Models\Alert;
use App\Models\Project;
use App\Services\Contracts\DockerServiceInterface;
use App\Services\Monitoring\HostMetricsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public array $containers = [];

    public array $host = [];

    public bool $dockerAvailable = true;

    public function mount(DockerServiceInterface $docker, HostMetricsService $hostMetrics)
    {
        $this->refresh($docker, $hostMetrics);
    }

    public function refresh(DockerServiceInterface $docker, HostMetricsService $hostMetrics)
    {
        $this->dockerAvailable = $docker->ping();

        $this->containers = $this->dockerAvailable ? $docker->listContainers(true) : [];
        $this->host = $hostMetrics->all();
    }

    public function render()
    {
        $running = collect($this->containers)->filter(fn ($c) => ($c['State'] ?? '') === 'running')->count();
        $stopped = collect($this->containers)->filter(fn ($c) => ($c['State'] ?? '') === 'exited')->count();
        $unhealthy = collect($this->containers)->filter(fn ($c) => ($c['Status'] ?? '') === 'unhealthy')->count();

        return view('livewire.dashboard', [
            'running' => $running,
            'stopped' => $stopped,
            'unhealthy' => $unhealthy,
            'projects' => Project::with('containers')->get(),
            'alerts' => Alert::where('status', 'firing')->latest()->limit(5)->get(),
        ]);
    }
}
