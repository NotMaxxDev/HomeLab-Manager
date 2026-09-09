@props(['color' => 'gray', 'label' => null])

@php
    $colors = [
        'gray' => 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-200',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
    ][$color] ?? 'bg-surface-100 text-surface-700';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors }}">
    {{ $label ?? $slot }}
</span>
