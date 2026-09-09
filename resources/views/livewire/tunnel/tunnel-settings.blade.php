<div class="space-y-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Tunnel & Reverse Proxy</h1>

    <div class="rounded-xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
        <h2 class="mb-4 text-lg font-semibold text-surface-900 dark:text-white">Cloudflare / Cloudflare Tunnel Status</h2>
        <div class="space-y-3">
            @forelse($tunnels as $tunnel)
                <div class="flex items-center justify-between rounded-lg border border-surface-200 p-4 dark:border-surface-800">
                    <div>
                        <h3 class="font-medium text-surface-900 dark:text-white">{{ $tunnel->name }}</h3>
                        <p class="text-xs text-surface-500">{{ $tunnel->domain }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-500">{{ $tunnel->status }}</span>
                </div>
            @empty
                <p class="text-sm text-surface-500">Keine Tunnel konfiguriert.</p>
            @endforelse
        </div>
    </div>
</div>
