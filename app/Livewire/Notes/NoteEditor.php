<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Notiz Editor')]
class NoteEditor extends Component
{
    public string $title = '';
    public string $content = '';
    public string $tags = '';
    public ?int $noteId = null;
    public string $mode = 'create';

    public function mount(Note $note = null)
    {
        if ($note && $note->exists) {
            $this->mode = 'edit';
            $this->noteId = $note->id;
            $this->title = $note->title;
            $this->content = $note->content;
        }
    }

    public function saveNote()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
        ]);

        $noteData = [
            'user_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->content,
            'slug' => str($this->title)->slug().'-'.strtolower(str()->random(4)),
        ];

        if ($this->mode === 'edit' && $this->noteId) {
            $note = Note::find($this->noteId);
            if ($note) {
                $note->update($noteData);
            }
        } else {
            Note::create($noteData);
        }

        return redirect()->route('notes.index');
    }

    public function render()
    {
        return view('livewire.note-editor');
    }
}
