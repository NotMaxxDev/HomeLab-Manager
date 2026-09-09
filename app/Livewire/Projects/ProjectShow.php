<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Projekt Details')]
class ProjectShow extends Component
{
    public Project $project;

    public function mount(Project $project)
    {
        $this->project = $project->load('containers');
    }

    public function render()
    {
        return view('livewire.projects.project-show');
    }
}
