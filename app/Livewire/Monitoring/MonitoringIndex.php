<?php

namespace App\Livewire\Monitoring;

use App\Models\Alert;
use App\Services\Monitoring\HostMetricsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Monitoring')]
class MonitoringIndex extends Component
{
    public array $host = [];

    public array $alerts = [];

    public function render(HostMetricsService $hostMetrics)
    {
        $this->host = $hostMetrics->all();
        $this->alerts = Alert::where('status', 'firing')->latest()->limit(5)->get();

        return view('livewire.monitoring.index');
    }
}