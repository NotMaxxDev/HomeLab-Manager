<div>
    @if(! $dockerAvailable)
        <x-alert type="warning" message="Docker-Engine ist nicht erreichbar. Prüfe den Socket-Pfad (DOCKER_SOCKET_PATH)."/>
    @endif

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-card class="p-5">
            <p class="text-sm text-surface-500">{{ __('dashboard.running') }}</p>
            <p class="mt-1 text-3xl font-semibold text-emerald-500">{{ $running }}</p>
        </x-card>
        <x-card class="p-5">
            <p class="text-sm text-surface-500">{{ __('dashboard.stopped') }}</p>
            <p class="mt-1 text-3xl font-semibold text-surface-400">{{ $stopped }}</p>
        </x-card>
        <x-card class="p-5">
            <p class="text-sm text-surface-500">{{ __('dashboard.unhealthy') }}</p>
            <p class="mt-1 text-3xl font-semibold text-red-500">{{ $unhealthy }}</p>
        </x-card>
        <x-card class="p-5">
            <p class="text-sm text-surface-500">{{ __('dashboard.total') }}</p>
            <p class="mt-1 text-3xl font-semibold text-indigo-500">{{ count($containers) }}</p>
        </x-card>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Host-Metriken --}}
        <x-card class="p-5">
            <h3 class="font-medium text-surface-900 dark:text-white">{{ __('dashboard.host') }}</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <x-metric-row :label="__('dashboard.cpu')" :value="number_format($host['cpu']['percent'] ?? 0, 1).' %'"/>
                <x-metric-row :label="__('dashboard.memory')" :value="number_format($host['memory']['percent'] ?? 0, 1).' %'"/>
                <x-metric-row :label="__('dashboard.load')" :value="number_format($host['load']['load1'] ?? 0, 2)"/>
                <x-metric-row :label="__('dashboard.uptime')" :value="formatUptime($host['uptime']['seconds'] ?? 0)"/>
            </dl>
        </x-card>

        {{-- Letzte Alerts --}}
        <x-card class="p-5">
            <h3 class="font-medium text-surface-900 dark:text-white">{{ __('dashboard.recent_alerts') }}</h3>
            @forelse($alerts as $alert)
                <div class="mt-3 border-l-2 border-red-500 pl-3">
                    <p class="text-sm text-surface-700 dark:text-surface-200">{{ $alert->message }}</p>
                    <p class="text-xs text-surface-400">{{ $alert->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="mt-3 text-sm text-surface-400">{{ __('dashboard.no_alerts') }}</p>
            @endforelse
        </x-card>

        {{-- Projekt-Kacheln --}}
        <x-card class="p-5">
            <h3 class="font-medium text-surface-900 dark:text-white">{{ __('dashboard.projects') }}</h3>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse($projects as $project)
                    <a href="{{ route('projects.show', $project) }}"
                       class="flex items-center gap-2 rounded-lg border border-surface-200 px-3 py-2 text-sm hover:bg-surface-50 dark:border-surface-700 dark:hover:bg-surface-800">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $project->color }}"></span>
                        <span class="text-surface-700 dark:text-surface-200">{{ $project->name }}</span>
                        <x-badge :color="$project->containers->where('status', 'running')->count() === $project->containers->count() && $project->containers->isNotEmpty() ? 'green' : 'gray'"
                                 :label="$project->containers->count()"/>
                    </a>
                @empty
                    <p class="text-sm text-surface-400">{{ __('dashboard.no_projects') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>
