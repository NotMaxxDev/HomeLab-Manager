@extends('layouts.guest')
@section('title', 'Setup')

@section('content')
    <h2 class="text-xl font-semibold text-surface-900 dark:text-white">Ersteinrichtung</h2>
    <p class="mt-1 text-sm text-surface-500">Lege das Administrator-Konto an. Es wird kein Standard-Passwort vergeben.</p>

    <form method="POST" action="{{ route('setup.store') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">Name</label>
            <input name="name" type="text" required autofocus value="{{ old('name') }}"
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">E-Mail-Adresse</label>
            <input name="email" type="email" required value="{{ old('email') }}"
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">Passwort</label>
            <input name="password" type="password" required
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-surface-700 dark:text-surface-200">Passwort bestätigen</label>
            <input name="password_confirmation" type="password" required
                   class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600">
            Administrator-Konto anlegen
        </button>
    </form>
@endsection
