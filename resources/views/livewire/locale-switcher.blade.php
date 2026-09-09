<div class="flex items-center rounded-lg border border-surface-200 dark:border-surface-700">
    @foreach(['de' => 'DE', 'en' => 'EN'] as $code => $label)
        <button wire:click="switch('{{ $code }}')"
                @class([
                    'px-2.5 py-1.5 text-xs font-medium',
                    'bg-indigo-500 text-white' => $current === $code,
                    'text-surface-500 hover:bg-surface-100 dark:hover:bg-surface-800' => $current !== $code,
                ])>{{ $label }}</button>
    @endforeach
</div>
