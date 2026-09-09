@props(['label', 'value'])

<div class="flex items-center justify-between">
    <dt class="text-surface-500 dark:text-surface-400">{{ $label }}</dt>
    <dd class="font-medium text-surface-900 dark:text-white">{{ $value }}</dd>
</div>
