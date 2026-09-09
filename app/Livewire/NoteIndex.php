<?php

namespace App\Livewire;

use App\Models\Note;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Notizen')]
class NoteIndex extends Component
{
    public array $notes = [];

    public string $search = '';

    public function render()
    {
        $query = Note::query();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                ->orWhere('content', 'like', "%{$this->search}%");
        }

        $this->notes = $query->latest()->paginate(15);

        return view('livewire.note-index');
    }
}