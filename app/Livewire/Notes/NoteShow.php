<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Notiz anzeigen')]
class NoteShow extends Component
{
    public Note $note;

    public function mount(Note $note)
    {
        $this->note = $note;
    }

    public function render()
    {
        return view('livewire.notes.note-show');
    }
}
