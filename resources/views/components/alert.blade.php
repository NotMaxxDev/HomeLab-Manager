@props(['type' => 'success', 'message' => null])

@php
    $styles = [
        'success' => 'border-emerald-500 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
        'error' => 'border-red-500 bg-red-500/10 text-red-600 dark:text-red-300',
        'warning' => 'border-amber-500 bg-amber-500/10 text-amber-600 dark:text-amber-300',
        'info' => 'border-blue-500 bg-blue-500/10 text-blue-600 dark:text-blue-300',
    ][$type] ?? 'border-surface-500';
@endphp

@if($message || session($type))
    <div class="rounded-lg border px-4 py-3 text-sm {{ $styles }}">
        {{ $message ?? session($type) }}
    </div>
@endif
