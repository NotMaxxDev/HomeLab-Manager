<?php

namespace App\Livewire\Docker;

use App\Services\Contracts\DockerServiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Volumes & Netzwerke')]
class VolumesNetworksIndex extends Component
{
    public array $volumes = [];
    public array $networks = [];

    public function mount(DockerServiceInterface $docker)
    {
        $this->refresh($docker);
    }

    public function refresh(DockerServiceInterface $docker)
    {
        if ($docker->ping()) {
            $this->volumes = $docker->listVolumes() ?? [];
            $this->networks = $docker->listNetworks() ?? [];
        }
    }

    public function render()
    {
        return view('livewire.docker.volumes-networks-index');
    }
}
