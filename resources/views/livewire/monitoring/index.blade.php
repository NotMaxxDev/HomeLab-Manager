<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    {{-- Host-Metriken --}}
    <x-card class="p-5">
        <h3 class="font-medium text-surface-900 dark:text-white">{{ __('monitoring.host') }}</h3>
        <dl class="mt-4 space-y-3 text-sm">
            <x-metric-row :label="__('monitoring.cpu')" :value="number_format($host['cpu']['percent'] ?? 0, 1).' %'"/>
            <x-metric-row :label="__('monitoring.memory')" :value="number_format($host['memory']['percent'] ?? 0, 1).' %'"/>
            <x-metric-row :label="__('monitoring.load')" :value="number_format($host['load']['load1'] ?? 0, 2)"/>
            <x-metric-row :label="__('monitoring.uptime')" :value="formatUptime($host['uptime']['seconds'] ?? 0)"/>
        </dl>
    </x-card>

    {{-- Aktive Alerts --}}
    <x-card class="p-5">
        <h3 class="font-medium text-surface-900 dark:text-white">{{ __('monitoring.active_alerts') }}</h3>
        @if($alerts->isNotEmpty())
            <ul class="space-y-2 text-sm text-surface-700 dark:text-surface-300">
                @foreach($alerts as $alert)
                    <li class="flex items-start">
                        <span class="rounded bg-red-100 text-red-600 text-xs font-medium px-2 py-0.5 mr-2">⚠</span>
                        <div>
                            <p class="font-medium">{{ $alert->message }}</p>
                            <p class="text-xs text-surface-400">{{ $alert->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-surface-400 dark:text-surface-500">{{ __('monitoring.no_alerts') }}</p>
        @endif
    </x-card>
</div>