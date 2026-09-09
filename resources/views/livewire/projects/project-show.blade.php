<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $project->name }}</h1>
            <p class="text-sm text-surface-500">{{ $project->description }}</p>
        </div>
        <a href="{{ route('projects.index') }}" class="rounded-lg border border-surface-300 bg-white px-4 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50 dark:border-surface-700 dark:bg-surface-800 dark:text-surface-200 dark:hover:bg-surface-700">Zurück</a>
    </div>

    <div class="rounded-xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
        <h2 class="mb-4 text-lg font-semibold text-surface-900 dark:text-white">Zugeordnete Container</h2>
        <div class="space-y-2">
            @forelse($project->containers as $container)
                <div class="flex items-center justify-between rounded-lg border border-surface-200 p-3 dark:border-surface-800">
                    <span class="font-medium text-surface-900 dark:text-white">{{ $container->name }}</span>
                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-500">{{ $container->status }}</span>
                </div>
            @empty
                <p class="text-sm text-surface-500">Keine Container diesem Projekt zugewiesen.</p>
            @endforelse
        </div>
    </div>
</div>
