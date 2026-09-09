@props(['href' => '#', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
   @class([
       'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
       'bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300' => $active,
       'text-surface-600 hover:bg-surface-100 hover:text-surface-900 dark:text-surface-300 dark:hover:bg-surface-800 dark:hover:text-white' => ! $active,
   ])>
    @if($icon)
        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <path d="{{ $icon }}"/>
        </svg>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
