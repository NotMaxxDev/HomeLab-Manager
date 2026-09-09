<div>
    {{-- Suchleiste --}}
    <div class="mb-4">
        <x-input type="text" placeholder="Nach Notizen suchen..." 
                 class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                 wire:model.debounce="search"
                 @disabled="true"
                 wire:ignore>
            <x-icon slot="suffix" name="search" class="text-surface-400 dark:text-surface-500"/>
        </div>
    </div>

    {{-- Notizen-Liste --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($notes as $note)
            <div class="group border rounded-lg p-3 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                {{-- Liefertes Header-Bild --}}
                {{-- Favorite-Sternchen --}}
                @if($note->is_favorite)
                    <span class="absolute top-2 right-2 text-indigo-500">{{-- Stern --}}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 9v2m0 9v2m-6-3h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                    @endif
                {{-- Titel --}}
                <h3 class="font-medium text-surface-900 dark:text-white group-hover:text-indigo-500 transition-colors line-clamp-1">{{ Str::limit($note->title, 50) }}</h3>
                {{-- Tags --}}
                @if($note->tags->isNotEmpty())
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($note->tags as $tag)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ Str::lower($tag->name) === 'docker' ? 'bg-indigo-100 text-indigo-600' : (Str::lower($tag->name) === 'homelab' ? 'bg-emerald-100 text-emerald-600' : 'bg-surface-200 text-surface-600 dark:bg-surface-700 dark:text-surface-300') }}">
                                <span>{{ $tag->name }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
        @if($notes->isEmpty())
            <p class="col-span-full text-center text-surface-400 dark:text-surface-500">{{ __('note.no_notes') }}</p>
        @endif
    </div>

    {{-- Pagination --}}
    @if($notes->hasMorePages())
        <div class="mt-6 flex justify-center">
            @foreach(range(1, $notes->lastPage()) as $page)
                @if($notes->onPage($page))
                    <span class="mx-1 px-2 py-1 rounded bg-indigo-500 text-white font-medium">{{ $page }}</span>
                @else>
                    <a href="?search={{ $notes->search() }}" class="mx-1 px-2 py-1 rounded text-surface-500 hover:bg-surface-100 dark:hover:bg-surface-800">{{ $page }}</a>
                @endif
            @endforeach>
        </div>
    @endif
</div>