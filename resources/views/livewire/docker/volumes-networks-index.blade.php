<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Docker Volumes & Netzwerke</h1>
        <button wire:click="refresh" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-600">Aktualisieren</button>
    </div>

    {{-- Volumes --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Volumes</h2>
        <div class="overflow-x-auto rounded-xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-50 text-xs font-semibold uppercase text-surface-500 dark:bg-surface-800/50 dark:text-surface-400">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">Mountpoint</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                    @forelse($volumes as $vol)
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30">
                            <td class="px-6 py-4 font-medium text-surface-900 dark:text-white">{{ $vol['Name'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-surface-500">{{ $vol['Driver'] ?? '-' }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-surface-500">{{ $vol['Mountpoint'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-surface-500">Keine Docker Volumes gefunden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Netzwerke --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Netzwerke</h2>
        <div class="overflow-x-auto rounded-xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-50 text-xs font-semibold uppercase text-surface-500 dark:bg-surface-800/50 dark:text-surface-400">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">Scope</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                    @forelse($networks as $net)
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30">
                            <td class="px-6 py-4 font-medium text-surface-900 dark:text-white">{{ $net['Name'] ?? '-' }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-surface-500">{{ substr($net['Id'] ?? '', 0, 12) }}</td>
                            <td class="px-6 py-4 text-surface-500">{{ $net['Driver'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-surface-500">{{ $net['Scope'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-surface-500">Keine Docker Netzwerke gefunden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
