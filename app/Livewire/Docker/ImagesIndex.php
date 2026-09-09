<?php

namespace App\Livewire\Docker;

use App\Services\Contracts\DockerServiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Docker Images')]
class ImagesIndex extends Component
{
    public array $images = [];

    public function mount(DockerServiceInterface $docker)
    {
        $this->refresh($docker);
    }

    public function refresh(DockerServiceInterface $docker)
    {
        $this->images = $docker->ping() ? $docker->listImages() : [];
    }

    public function render()
    {
        return view('livewire.docker.images-index');
    }
}
