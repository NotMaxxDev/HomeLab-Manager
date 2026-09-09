<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Docker Images</h1>
        <button wire:click="refresh" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-600">Aktualisieren</button>
    </div>

    <div class="overflow-x-auto rounded-xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-50 text-xs font-semibold uppercase text-surface-500 dark:bg-surface-800/50 dark:text-surface-400">
                <tr>
                    <th class="px-6 py-3">Repository / Tag</th>
                    <th class="px-6 py-3">Image ID</th>
                    <th class="px-6 py-3">Größe</th>
                    <th class="px-6 py-3">Erstellt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                @forelse($images as $img)
                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30">
                        <td class="px-6 py-4 font-medium text-surface-900 dark:text-white">{{ $img['RepoTags'][0] ?? '<none>' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-surface-500">{{ substr($img['Id'] ?? '', 7, 12) }}</td>
                        <td class="px-6 py-4 text-surface-500">{{ isset($img['Size']) ? round($img['Size'] / 1024 / 1024, 2) . ' MB' : '-' }}</td>
                        <td class="px-6 py-4 text-surface-500">{{ isset($img['Created']) ? date('Y-m-d H:i', $img['Created']) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-surface-500">Keine Docker Images gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
