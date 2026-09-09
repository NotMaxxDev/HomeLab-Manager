<div class="space-y-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Einstellungen</h1>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <a href="{{ route('settings.llm') }}" class="rounded-xl border border-surface-200 bg-white p-6 shadow-sm hover:border-indigo-500 dark:border-surface-800 dark:bg-surface-900">
            <h2 class="font-semibold text-surface-900 dark:text-white">LLM Provider</h2>
            <p class="mt-1 text-sm text-surface-500">Konfiguriere API-Keys für OpenAI, Anthropic und lokale Modelle.</p>
        </a>

        <a href="{{ route('settings.notifications') }}" class="rounded-xl border border-surface-200 bg-white p-6 shadow-sm hover:border-indigo-500 dark:border-surface-800 dark:bg-surface-900">
            <h2 class="font-semibold text-surface-900 dark:text-white">Benachrichtigungen</h2>
            <p class="mt-1 text-sm text-surface-500">Verwalte Telegram, Discord oder Webhook Benachrichtigungen.</p>
        </a>
    </div>
</div>
