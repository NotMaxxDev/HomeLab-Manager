@extends('layouts.guest')
@section('title', __('auth.two_factor'))

@section('content')
    <h2 class="text-xl font-semibold text-surface-900 dark:text-white">{{ __('auth.two_factor') }}</h2>
    <p class="mt-1 text-sm text-surface-500">{{ __('auth.two_factor_subtitle') }}</p>

    <form method="POST" action="{{ url('/two-factor-challenge') }}" class="mt-6 space-y-4" x-data="{ recovery: false }">
        @csrf

        <div x-show="! recovery">
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('auth.code') }}</label>
            <input name="code" type="text" inputmode="numeric" autofocus autocomplete="one-time-code"
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div x-show="recovery" x-cloak>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('auth.recovery_code') }}</label>
            <input name="recovery_code" type="text" autocomplete="one-time-code"
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('recovery_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600">
            {{ __('auth.confirm') }}
        </button>
        <button type="button" @click="recovery = ! recovery" class="w-full text-sm text-indigo-500 hover:underline">
            <span x-show="! recovery">{{ __('auth.use_recovery_code') }}</span>
            <span x-show="recovery">{{ __('auth.use_code') }}</span>
        </button>
    </form>
@endsection
