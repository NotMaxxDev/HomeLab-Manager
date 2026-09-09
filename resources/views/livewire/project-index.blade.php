<div class="space-y-4">
    @if(empty($projects))
        <p class="text-surface-400 dark:text-surface-500">{{ __('project.no_projects') }}</p>
    @else
        @foreach($projects as $project)
            <a href="{{ route('projects.show', $project) }}"
               class="border rounded-lg p-4 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                <div class="flex items-start gap-3">
                    {{-- Color Badge --}}
                    <span class="h-6 w-6 rounded-full" style="background-color: {{ $project->color }}"></span>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-surface-900 dark:text-white line-clamp-1">{{ $project->name }}</h3>
                        @if($project->description)
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-300 line-clamp-1">{{ $project->description }}</p>
                        @endif
                    </div>
                    {{-- Kachel-Info --}}
                    <div class="ml-3 text-right">
                        <x-badge :color="gray" :label="$project->containers->count()"/>
                    </div>
                </div>
            </a>
        @endforeach
    @endif
</div>