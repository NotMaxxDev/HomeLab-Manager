<div class="space-y-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Benutzerverwaltung</h1>

    <div class="overflow-x-auto rounded-xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-50 text-xs font-semibold uppercase text-surface-500 dark:bg-surface-800/50 dark:text-surface-400">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">E-Mail</th>
                    <th class="px-6 py-3">Rolle</th>
                    <th class="px-6 py-3">Registriert</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                @foreach($users as $user)
                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30">
                        <td class="px-6 py-4 font-medium text-surface-900 dark:text-white">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-surface-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs font-medium text-indigo-500">
                                {{ $user->roles->pluck('name')->join(', ') ?: 'Standard' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-surface-500">{{ $user->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
