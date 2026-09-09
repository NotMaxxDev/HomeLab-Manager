<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $note->title }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('notes.edit', $note) }}" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-600">Bearbeiten</a>
            <a href="{{ route('notes.index') }}" class="rounded-lg border border-surface-300 bg-white px-4 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50 dark:border-surface-700 dark:bg-surface-800 dark:text-surface-200 dark:hover:bg-surface-700">Zurück</a>
        </div>
    </div>

    <div class="rounded-xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
        <div class="prose max-w-none text-surface-700 dark:text-surface-300">
            {!! nl2br(e($note->content)) !!}
        </div>
    </div>
</div>
