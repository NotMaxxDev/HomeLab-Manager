<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full">
<div class="flex min-h-full flex-col items-center justify-center bg-surface-100 px-4 py-12 dark:bg-surface-950">
    <div class="w-full max-w-md">
        <div class="mb-8 flex flex-col items-center gap-3">
            <svg class="h-12 w-12 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 9h8M8 13h5"/></svg>
            <h1 class="text-2xl font-semibold text-surface-900 dark:text-white">Homelab<span class="text-indigo-500">Manager</span></h1>
        </div>
        <x-card class="p-6">
            @yield('content')
        </x-card>
    </div>
</div>
@livewireScripts
</body>
</html>
