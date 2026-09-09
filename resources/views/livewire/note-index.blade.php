<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Notizen & Dokumentation</h1>
        <a href="{{ route('notes.create') }}" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-600">Neue Notiz</a>
    </div>

    {{-- Suchleiste --}}
    <div>
        <input type="text" placeholder="Nach Notizen suchen..." 
               class="w-full rounded-lg border border-surface-300 bg-white px-4 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
               wire:model.live.debounce.300ms="search">
    </div>

    {{-- Notizen-Liste --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($notes as $note)
            <a href="{{ route('notes.show', $note) }}" class="group rounded-xl border border-surface-200 bg-white p-5 shadow-sm transition hover:border-indigo-500 dark:border-surface-800 dark:bg-surface-900 dark:hover:border-indigo-500">
                <h3 class="font-semibold text-surface-900 group-hover:text-indigo-500 dark:text-white">{{ Str::limit($note->title, 50) }}</h3>
                <p class="mt-2 line-clamp-3 text-sm text-surface-500">{{ Str::limit(strip_tags($note->content), 120) }}</p>
                <div class="mt-4 flex items-center justify-between text-xs text-surface-400">
                    <span>{{ $note->created_at?->diffForHumans() }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-surface-200 bg-white p-8 text-center text-surface-500 dark:border-surface-800 dark:bg-surface-900">
                Keine Notizen vorhanden.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $notes->links() }}
    </div>
</div>