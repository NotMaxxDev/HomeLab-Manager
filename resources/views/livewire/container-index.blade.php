<div>
    {{-- Filter --}}
    <div class="mb-4">
        <x-input type="text" placeholder="Filter nach Namen oder Image..." 
                 class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                 wire:model.debounce.300ms="filter"
                 @disabled="! $dockerAvailable"
                 wire:ignore>
        </div>

        {{-- Polling-Indicator --}}
        @if($dockerAvailable)
            <div class="mt-2 text-xs text-surface-400" wire:poll="{{$pollInterval}}ms">
                Automatische Aktualisierung alle {{$pollInterval/1000}} Sek.
            </div>
        @endif
    </div>

    {{-- Tabellen-Header --}}
    <div class="overflow-x-auto rounded-lg border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
        <table class="min-w-full text-sm text-surface-600 dark:text-surface-300">
            <thead class="bg-surface-100 dark:bg-surface-800 text-left text-xs font-medium uppercase">
                <tr>
                    <th scope="col" class="py-3 px-4">{{ __('container.name') }}</th>
                    <th scope="col" class="py-3 px-4">{{ __('container.image') }}</th>
                    <th scope="col" class="py-3 px-4">{{ __('container.status') }}</th>
                    <th scope="col" class="py-3 px-4">{{ __('container.health') }}</th>
                    <th scope="col" class="py-3 px-4 text-right">{{ __('container.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @if(empty($containers))
                <tr>
                    <td colspan="5" class="py-6 text-center text-surface-400 dark:text-surface-500">{{ __('container.no_containers') }}</td>
                </tr>
            @else
                @foreach($containers as $container)
                    @php
                        $name = ltrim(implode(',', $container['Names'] ?? []), '/');
                        $image = $container['Image'] ?? 'unknown';
                        $status = $container['Status'] ?? 'unknown';
                        $health = $container['Health']['Status'] ?? 'none';
                        $ports = $container['Ports'] ?? [];
                        $networks = $container['Networks'] ?? [];
                        $compose = isset($container['Labels']['com.docker.compose.project']) ? $container['Labels']['com.docker.compose-project'] : null;
                    @endphp>

                    <tr class="border-b dark:border-surface-700 cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800">
                        <td class="py-3 px-4">
                            <strong class="text-surface-900 dark:text-white">{{ $name }}</strong>
                            @if($compose)
                                <span class="ml-1 text-xs text-indigo-500/70 dark:text-indigo-300/70 badge bg-indigo-500/10 text-indigo-500/70 rounded">{{ __('container.compose_project') }}: {{ $compose }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-surface-600 dark:text-surface-300">
                            {{ substr($image, 0, 30) }}{@if(strlen($image) > 30)}...{@endif}
                        </td>
                        <td class="py-3 px-4">
                            @if(str_contains($status, 'running'))
                                <span class="inline-flex items-center rounded bg-emerald-100 text-emerald-600 text-xs font-medium">{{ str_replace('running', '', ucfirst($status)) }}</span>
                            @elseif(str_contains($status, 'paused'))
                                <span class="inline-flex items-center rounded bg-amber-100 text-amber-600 text-xs font-medium">Paused</span>
                            @elseif(str_contains($status, 'exited'))
                                <span class="inline-flex items-center rounded bg-red-100 text-red-600 text-xs font-medium">{{ str_replace('exited', '', ucfirst($status)) }} ({{ $container['State']['ExitCode'] ?? 0 }})</span>
                            @else
                                <span class="inline-flex items-center rounded bg-indigo-100 text-indigo-600 text-xs font-medium">{{ $status }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-surface-600 dark:text-surface-300">
                            @if($health !== 'none')
                                @if($health === 'unhealthy')
                                    <span class="rounded bg-red-100 text-red-600 text-xs font-medium rounded">{{ $health }}</span>
                                @else
                                    <span class="rounded bg-green-100 text-green-600 text-xs font-medium rounded">{{ $health }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="py-3 px-4 text-surface-600 dark:text-surface-300 whitespace-nowrap">
                            @foreach($ports as $port)
                                @if($port['PrivatePort'])
                                    {{ $port['PrivatePort'] }}{@if($port['Type'] !== '']) {{{ $port['Type'] }}}@{endför @endif
                                @endif
                            @endforeach
                            {{ count($networks) }} Netze
                        </td>
                        <td class="py-3 px-4 text-right">
                            {{-- Buttons: Start/Stop/Restart mit Bestätigung --}}
                            @if($dockerAvailable)
                                <div class="flex gap-1">
                                    {{-- Start --}}
                                    @if($status !== 'running')
                                        <button class="rounded bg-emerald-500 text-emerald-600 text-xs font-medium px-2 py-1"
                                                onclick="confirmAction('{{ $name }}', 'start', '{{ route('containers.start', $container['Id'] ?? $name) }}')">
                                            {{ __('container.start') }}
                                        </button>
                                    @endif
                                    {{-- Stop --}}
                                    @if($status !== 'exited' && $status !== 'paused')
                                        <button class="rounded bg-red-500 text-red-600 text-xs font-medium px-2 py-1"
                                                onclick="confirmAction('{{ $name }}', 'stop', '{{ route('containers.stop', $container['Id'] ?? $name) }}')">
                                            {{ __('container.stop') }}
                                        </button>
                                    @endif
                                    {{-- Restart --}}
                                    <button class="rounded bg-indigo-500 text-indigo-600 text-xs font-medium px-2 py-1"
                                            onclick="confirmAction('{{ $name }}', 'restart', '{{ route('containers.restart', $container['Id'] ?? $name) }}')">
                                        {{ __('container.restart') }}
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>

    {{-- Log-Ansicht --}}
    @if($showLogs && $selectedContainer)
        <div class="mt-4 p-4 rounded-lg border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-900 max-h-96 overflow-y-auto text-xs text-surface-500 dark:text-surface-400">
            <div class="flex items-center justify-between mb-3">
                <span>{{ __('container.logs_titel') }}: <strong>{{ $selectedContainer }}</strong></span>
                <button class="text-sm text-surface-500 hover:underline cursor-pointer" @click="closeLogs()">Schließen</button>
            </div>
            {{-- Live Logs via Docker API --}}
            {{-- Wir holen uns die Logs über die Docker Engine API --}}
            {{-- Da Guzzle-Stats hier keine Streaming-Logs liefern können, holen wir per HTTP GET /containers/{id}/logs --}}
            {{-- Für lokale Anzeige nutzen wir das Polling-Flag und laden bei Bedarf manuell nach. --}}
            <p class="mt-2 text-xs text-surface-400">{{ __('container.logs_placeholder') }}</p>
        </div>
    @endif
</div>

<script>
    // Bestätigungs-Dialog für destructive Aktionen
    function confirmAction(containerName, action, url) {
        if (confirm('{{ __('container.confirm_action') }} ' + containerName + ' {{ __('container.' + action + '.confirm') }}')) {
            // AJAX POST zur Docker-Action
            // Hinweis: In einem produktiven Setup könnte ein CSRF-Token erforderlich sein.
            // Hier vereinfacht per Fetch mit Headern für Livewire/CSRF.
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text || 'Fehler'); });
                }
                return response.json();
            })
            .then(data => {
                // UI nach Aktion aktualisieren
                location.reload();
            })
            .catch(error => {
                console.error('Fehler:', error);
                alert('Fehler bei der Aktion: ' + error.message);
            });
        }
    }
</script>