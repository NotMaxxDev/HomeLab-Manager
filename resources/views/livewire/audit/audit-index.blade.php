<div class="space-y-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Audit-Logs</h1>

    <div class="overflow-x-auto rounded-xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-50 text-xs font-semibold uppercase text-surface-500 dark:bg-surface-800/50 dark:text-surface-400">
                <tr>
                    <th class="px-6 py-3">Zeitpunkt</th>
                    <th class="px-6 py-3">Benutzer</th>
                    <th class="px-6 py-3">Aktion</th>
                    <th class="px-6 py-3">IP-Adresse</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                @forelse($logs as $log)
                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30">
                        <td class="px-6 py-4 text-surface-500">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4 font-medium text-surface-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-surface-500">{{ $log->action }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-surface-500">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-surface-500">Keine Audit-Logs protokolliert.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
