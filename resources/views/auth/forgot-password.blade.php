@extends('layouts.guest')
@section('title', __('auth.forgot_password'))

@section('content')
    <h2 class="text-xl font-semibold text-surface-900 dark:text-white">{{ __('auth.forgot_password') }}</h2>
    <p class="mt-1 text-sm text-surface-500">{{ __('auth.forgot_password_subtitle') }}</p>

    @if (session('status'))
        <x-alert type="success" :message="session('status')"/>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('auth.email') }}</label>
            <input name="email" type="email" required autofocus class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600">{{ __('auth.send_reset_link') }}</button>
    </form>
@endsection
