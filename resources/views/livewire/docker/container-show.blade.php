<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $container['Name'] ?? $containerId }}</h1>
            <p class="text-sm text-surface-500">ID: {{ substr($containerId, 0, 12) }}</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="start" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Starten</button>
            <button wire:click="restart" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Neustarten</button>
            <button wire:click="stop" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Stoppen</button>
        </div>
    </div>

    <div class="rounded-xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
        <h2 class="mb-4 text-lg font-semibold text-surface-900 dark:text-white">Container Logs</h2>
        <pre class="max-h-96 overflow-y-auto rounded-lg bg-surface-950 p-4 font-mono text-xs text-emerald-400">{{ $logs ?: 'Keine Logs verfügbar.' }}</pre>
    </div>
</div>
