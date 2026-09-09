<div class="space-y-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Profil & Einstellungen</h1>

    <div class="rounded-xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 space-y-4">
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-200">Name</label>
            <input type="text" value="{{ $name }}" disabled class="mt-1 w-full rounded-lg border border-surface-300 bg-surface-100 px-3 py-2 text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-200">E-Mail</label>
            <input type="email" value="{{ $email }}" disabled class="mt-1 w-full rounded-lg border border-surface-300 bg-surface-100 px-3 py-2 text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
        </div>
    </div>
</div>
