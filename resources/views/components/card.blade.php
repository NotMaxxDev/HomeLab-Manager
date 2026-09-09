@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-surface-200 bg-white shadow-sm dark:border-surface-800 dark:bg-surface-900 '.$class]) }}>
    {{ $slot }}
</div>
