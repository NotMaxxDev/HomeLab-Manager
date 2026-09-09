@extends('layouts.guest')
@section('title', __('auth.reset_password'))

@section('content')
    <h2 class="text-xl font-semibold text-surface-900 dark:text-white">{{ __('auth.reset_password') }}</h2>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('auth.email') }}</label>
            <input name="email" type="email" required value="{{ old('email', $request->email) }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('auth.password') }}</label>
            <input name="password" type="password" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">{{ __('auth.confirm_password') }}</label>
            <input name="password_confirmation" type="password" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600">{{ __('auth.reset_password') }}</button>
    </form>
@endsection
