<div class="space-y-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">LLM Provider Konfiguration</h1>

    <div class="rounded-xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
        <div class="space-y-4">
            @forelse($providers as $provider)
                <div class="flex items-center justify-between rounded-lg border border-surface-200 p-4 dark:border-surface-800">
                    <div>
                        <h3 class="font-medium text-surface-900 dark:text-white">{{ $provider->name }}</h3>
                        <p class="text-xs text-surface-500">{{ $provider->model }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-surface-500">Keine LLM Provider hinterlegt.</p>
            @endforelse
        </div>
    </div>
</div>
