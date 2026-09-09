<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\Contracts\DockerServiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Projekte')]
class ProjectIndex extends Component
{
    public array $projects = [];

    public function render()
    {
        $this->projects = Project::with('containers')->latest()->get();

        return view('livewire.project-index');
    }
}