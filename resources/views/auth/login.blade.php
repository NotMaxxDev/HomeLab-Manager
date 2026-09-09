@extends('layouts.guest')
@section('title', __('auth.login'))

@section('content')
    <h2 class="text-xl font-semibold text-surface-900 dark:text-white">{{ __('auth.login') }}</h2>
    <p class="mt-1 text-sm text-surface-500">{{ __('auth.login_subtitle') }}</p>

    @if (session('status'))
        <x-alert type="info" :message="session('status')"/>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200" for="email">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" required autofocus autocomplete="username"
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200" for="password">{{ __('auth.password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-300">
            <input type="checkbox" name="remember" class="rounded border-surface-300 text-indigo-500 focus:ring-indigo-500">
            {{ __('auth.remember') }}
        </label>
        <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('auth.login') }}
        </button>
    </form>

    @if (Route::has('password.request'))
        <p class="mt-4 text-center text-sm">
            <a href="{{ route('password.request') }}" class="text-indigo-500 hover:underline">{{ __('auth.forgot_password') }}</a>
        </p>
    @endif
@endsection
