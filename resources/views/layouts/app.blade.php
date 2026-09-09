@php($theme = auth()->user()?->theme ?? session('theme') ?? 'dark')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" @class(['dark' => $theme === 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} · HomelabManager</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2'><rect x='3' y='4' width='18' height='16' rx='2'/><path d='M8 9h8M8 13h5'/></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full">
<div x-data="{ open: false, theme: '{{ $theme }}' }" class="flex h-full overflow-hidden">
    {{-- Sidebar --}}
    <aside class="hidden w-64 shrink-0 flex-col border-r border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 lg:flex">
        <div class="flex h-16 items-center gap-2 border-b border-surface-200 px-5 dark:border-surface-800">
            <svg class="h-7 w-7 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 9h8M8 13h5"/></svg>
            <span class="text-lg font-semibold text-surface-900 dark:text-white">Homelab<span class="text-indigo-500">Manager</span></span>
        </div>
        <nav class="scrollbar-thin flex-1 overflow-y-auto px-3 py-4">
            @include('layouts.partials.nav')
        </nav>
        <div class="border-t border-surface-200 p-3 dark:border-surface-800">
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-surface-100 dark:hover:bg-surface-800">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}</span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-medium text-surface-900 dark:text-white">{{ auth()->user()->name }}</span>
                    <span class="block truncate text-xs text-surface-500">{{ auth()->user()->roles->pluck('name')->map(fn($r) => __("roles.$r"))->join(', ') }}</span>
                </span>
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex h-16 shrink-0 items-center gap-4 border-b border-surface-200 bg-white px-4 dark:border-surface-800 dark:bg-surface-900 lg:px-6">
            <button @click="open = true" class="rounded-lg p-2 text-surface-500 hover:bg-surface-100 lg:hidden dark:hover:bg-surface-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="text-lg font-semibold text-surface-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h1>
            <div class="ml-auto flex items-center gap-2">
                <livewire:theme-toggle />
                <livewire:locale-switcher />
            </div>
        </header>
        <main class="scrollbar-thin flex-1 overflow-y-auto p-4 lg:p-6">
            {{ $slot }}
        </main>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-40 lg:hidden" @click="open = false">
        <div class="absolute inset-0 bg-surface-900/50"></div>
        <aside class="absolute left-0 top-0 h-full w-64 bg-white dark:bg-surface-900" @click.stop>
            <div class="flex h-16 items-center justify-between px-5">
                <span class="text-lg font-semibold">HomelabManager</span>
                <button @click="open = false" class="rounded p-1 text-surface-500">&times;</button>
            </div>
            <nav class="px-3 py-4">
                @include('layouts.partials.nav')
            </nav>
        </aside>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
