<?php

namespace App\Livewire\Docker;

use App\Services\Contracts\DockerServiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Container Details')]
class ContainerShow extends Component
{
    public string $containerId;
    public array $container = [];
    public string $logs = '';

    public function mount(string $container, DockerServiceInterface $docker)
    {
        $this->containerId = $container;
        $this->loadDetails($docker);
    }

    public function loadDetails(DockerServiceInterface $docker)
    {
        $this->container = $docker->inspectContainer($this->containerId) ?? [];
        $this->logs = $docker->getLogs($this->containerId, 100) ?? '';
    }

    public function start(DockerServiceInterface $docker)
    {
        $docker->startContainer($this->containerId);
        $this->loadDetails($docker);
    }

    public function stop(DockerServiceInterface $docker)
    {
        $docker->stopContainer($this->containerId);
        $this->loadDetails($docker);
    }

    public function restart(DockerServiceInterface $docker)
    {
        $docker->restartContainer($this->containerId);
        $this->loadDetails($docker);
    }

    public function render()
    {
        return view('livewire.docker.container-show');
    }
}
