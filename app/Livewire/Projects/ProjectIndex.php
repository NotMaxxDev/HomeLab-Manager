<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Projekte')]
class ProjectIndex extends Component
{
    public $projects = [];

    public function render()
    {
        $this->projects = Project::with('containers')->latest()->get();

        return view('livewire.project-index');
    }
}
