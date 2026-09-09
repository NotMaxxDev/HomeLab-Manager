<div class="space-y-4">
    {{-- Titel --}}
    <div>
        <x-input type="text" placeholder="Titel" wire:model="title"
                 class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"/>
    </div>

    {{-- Markdown Editor --}}
    <div>
        <x-label class="block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('note.content') }}</x-label>
        <textarea name="content" rows="10" class="w-full rounded-lg border border-surface-300 bg-white resize-none px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white wire:model.debounce.content"
                  placeholder="Inhalt in Markdown (Unterstriche _ für Kursiv, ** für Fett, ``` für Codeblöcke)"></textarea>
    </div>

    {{-- Vorschau --}}
    <div class="border rounded-lg p-4 min-h-[200px] surface-bg prose dark:prose-invert">
        {!! $html !!}

        {{-- Syntax-Highlighting nachladen --}}
        <script>window.hljs.highlightAll();</script>
    </div>

    {{-- Tags --}}
    <div>
        <x-label class="block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('note.tags') }}</x-label>
        <input name="tags" type="text" placeholder="Tags durch Komma getrennt (z. B. docker, homelab, monitoring)"
               class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
               wire:model="tags" wire:ignore>
    </div>

    {{-- Aktionen --}}
    <div class="flex gap-2">
        <button type="button" wire:click="saveNote"
                class="flex-1 rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ $this->mode === 'create' ? __('note.create') : __('note.edit') }}
        </button>
        @if($this->mode === 'edit')
            <button type="button" wire:click="reset"
                    class="rounded border border-surface-300 px-4 py-2 text-sm text-surface-600 hover:bg-surface-100 dark:hover:bg-surface-800">
                Abbrechen
            </button>
        @endif
    </div>
</div>

<script>
    // Syntax-Highlighting nach Markdown-Render aktivieren
    document.addEventListener('alpine:init', () => {
        window.hljs.highlightAll();
    });
</script>