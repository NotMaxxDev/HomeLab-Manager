<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notizen')]
class NoteIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $query = Note::query();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                ->orWhere('content', 'like', "%{$this->search}%");
        }

        return view('livewire.note-index', [
            'notes' => $query->latest()->paginate(15)
        ]);
    }
}
