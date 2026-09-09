<?php

namespace App\Livewire;

use App\Models\Note;
use Livewire\Component;

class NoteEditor extends Component
{
    public string $title = '';

    public string $content = '';

    public string $tags = '';

    public ?int $noteId = null;

    public string $mode = 'create'; // 'create' or 'edit'

    protected $listeners = [
        'save' => 'saveNote',
    ];

    public function mount(?string $noteId = null)
    {
        $this->mode = $noteId ? 'edit' : 'create';

        if ($noteId) {
            $note = Note::where('id', $noteId)
                ->orWhere('slug', $noteId)
                ->first();

            if ($note) {
                $this->title = $note->title;
                $this->content = $note->content;
                $this->tags = $note->tags->implode(', ');
                $this->noteId = $note->id;
            }
        }
    }

    public function saveNote()
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
            'tags' => ['string'],
        ]);

        $tags = collect(explode(',', $data['tags']))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->each(fn ($t) => Tag::firstOrCreate(['name' => $t]));

        $noteData = [
            'title' => $data['title'],
            'content' => $data['content'],
            'slug' => str($data['title'])->slug().'-'.strtolower(str()->random(4)),
        ];

        if ($this->mode === 'edit' && $this->noteId) {
            $note = Note::find($this->noteId);
            if ($note) {
                $note->update($noteData + ['tags' => $tags]);
            }
        } else {
            Note::create(array_merge($noteData, ['tags' => $tags]));
        }

        $this->dispatch('note-saved');
        $this->reset();
    }

    public function render()
    {
        $html = '';

        if ($this->content) {
            $html = marked.parse($this->content);

            // Syntax Highlighting nach Render nachladen
            // (werde nachher per Alpine-Init nachgeholt)
        }

        return view('livewire.note-editor', [
            'html' => $html,
        ]);
    }
}