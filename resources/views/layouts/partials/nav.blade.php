@php
    $user = auth()->user();
    $can = fn(string $p) => $user?->can($p);
@endphp

<x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10">
    {{ __('nav.dashboard') }}
</x-nav-link>

@if($can('docker.view'))
    <x-nav-section>{{ __('nav.docker') }}</x-nav-section>
    <x-nav-link href="{{ route('containers.index') }}" :active="request()->routeIs('containers.*')" icon="M4 6h16M4 12h16M4 18h16">
        {{ __('nav.containers') }}
    </x-nav-link>
    <x-nav-link href="{{ route('images.index') }}" :active="request()->routeIs('images.*')" icon="M4 16l4-4 4 4 8-8M4 8v8h16V8">
        {{ __('nav.images') }}
    </x-nav-link>
    <x-nav-link href="{{ route('volumes.index') }}" :active="request()->routeIs('volumes.*','networks.*')" icon="M12 3v18M3 12h18">
        {{ __('nav.volumes_networks') }}
    </x-nav-link>
@endif

@if($can('projects.view'))
    <x-nav-section>{{ __('nav.projects') }}</x-nav-section>
    <x-nav-link href="{{ route('projects.index') }}" :active="request()->routeIs('projects.*')" icon="M3 7h4v4H3zM10 7h11M3 15h4v4H3zM10 15h11">
        {{ __('nav.projects') }}
    </x-nav-link>
@endif

@if($can('notes.view'))
    <x-nav-section>{{ __('nav.knowledge') }}</x-nav-section>
    <x-nav-link href="{{ route('notes.index') }}" :active="request()->routeIs('notes.*')" icon="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z">
        {{ __('nav.notes') }}
    </x-nav-link>
@endif

@if($can('monitoring.view'))
    <x-nav-section>{{ __('nav.operations') }}</x-nav-section>
    <x-nav-link href="{{ route('monitoring.index') }}" :active="request()->routeIs('monitoring.*')" icon="M3 12h4l3 8 4-16 3 8h4">
        {{ __('nav.monitoring') }}
    </x-nav-link>
@endif

@if($can('chat.use'))
    <x-nav-link href="{{ route('chat.index') }}" :active="request()->routeIs('chat.*')" icon="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z">
        {{ __('nav.chat') }}
    </x-nav-link>
@endif

@if($can('audit.view') || $can('mcp.manage') || $can('tunnel.manage') || $can('settings.manage') || $can('users.manage'))
    <x-nav-section>{{ __('nav.administration') }}</x-nav-section>
    @if($can('audit.view'))
        <x-nav-link href="{{ route('audit.index') }}" :active="request()->routeIs('audit.*')" icon="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            {{ __('nav.audit') }}
        </x-nav-link>
    @endif
    @if($can('mcp.manage'))
        <x-nav-link href="{{ route('mcp.index') }}" :active="request()->routeIs('mcp.*')" icon="M4 7h16M4 12h16M4 17h10">
            {{ __('nav.mcp') }}
        </x-nav-link>
    @endif
    @if($can('tunnel.manage'))
        <x-nav-link href="{{ route('tunnel.index') }}" :active="request()->routeIs('tunnel.*')" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
            {{ __('nav.tunnel') }}
        </x-nav-link>
    @endif
    @if($can('users.manage'))
        <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" icon="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75">
            {{ __('nav.users') }}
        </x-nav-link>
    @endif
    @if($can('settings.manage'))
        <x-nav-link href="{{ route('settings.index') }}" :active="request()->routeIs('settings.*')" icon="M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33h.09a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82v.09a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z">
            {{ __('nav.settings') }}
        </x-nav-link>
    @endif
@endif

<div class="my-3 border-t border-surface-200 dark:border-surface-800"></div>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-surface-600 hover:bg-surface-100 dark:text-surface-300 dark:hover:bg-surface-800">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        {{ __('nav.logout') }}
    </button>
</form>
