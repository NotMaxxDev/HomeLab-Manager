<div class="flex h-[calc(100vh-8rem)] gap-6">
    {{-- Conversational List --}}
    <div class="w-64 shrink-0 rounded-xl border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
        <h2 class="mb-4 font-semibold text-surface-900 dark:text-white">Gespräche</h2>
        <div class="space-y-2">
            @forelse($conversations as $conv)
                <a href="{{ route('chat.show', $conv) }}" class="block rounded-lg p-2 text-sm text-surface-700 hover:bg-surface-100 dark:text-surface-300 dark:hover:bg-surface-800">
                    {{ $conv->title ?? 'Neuer Chat' }}
                </a>
            @empty
                <p class="text-xs text-surface-500">Keine vorherigen Chats.</p>
            @endforelse
        </div>
    </div>

    {{-- Chat Box --}}
    <div class="flex flex-1 flex-col rounded-xl border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
        <div class="flex-1 overflow-y-auto space-y-4 p-2">
            @forelse($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xl rounded-xl p-3 text-sm {{ $msg['role'] === 'user' ? 'bg-indigo-500 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-900 dark:text-white' }}">
                        {{ $msg['content'] }}
                    </div>
                </div>
            @empty
                <div class="flex h-full items-center justify-center text-sm text-surface-500">
                    Starte eine Unterhaltung mit dem Homelab AI Assistenten.
                </div>
            @endforelse
        </div>

        <form wire:submit.prevent="sendMessage" class="mt-4 flex gap-2">
            <input type="text" wire:model="message" placeholder="Nachricht eingeben..." class="flex-1 rounded-lg border border-surface-300 bg-white px-4 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            <button type="submit" class="rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600">Senden</button>
        </form>
    </div>
</div>
