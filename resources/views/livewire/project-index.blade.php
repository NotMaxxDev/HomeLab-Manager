<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Projekte</h1>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="group rounded-xl border border-surface-200 bg-white p-5 shadow-sm transition hover:border-indigo-500 dark:border-surface-800 dark:bg-surface-900 dark:hover:border-indigo-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="h-4 w-4 rounded-full" style="background-color: {{ $project->color ?? '#6366f1' }}"></span>
                        <h3 class="font-semibold text-surface-900 group-hover:text-indigo-500 dark:text-white">{{ $project->name }}</h3>
                    </div>
                    <span class="rounded-full bg-surface-100 px-2.5 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-300">
                        {{ $project->containers->count() }} Container
                    </span>
                </div>
                @if($project->description)
                    <p class="mt-3 line-clamp-2 text-sm text-surface-500">{{ $project->description }}</p>
                @endif
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-surface-200 bg-white p-8 text-center text-surface-500 dark:border-surface-800 dark:bg-surface-900">
                Keine Projekte vorhanden.
            </div>
        @endforelse
    </div>
</div>