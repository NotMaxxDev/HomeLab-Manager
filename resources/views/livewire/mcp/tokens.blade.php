<div>
    {{-- Token-Liste --}}
    <div class="overflow-x-auto rounded-lg border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
        <table class="min-w-full text-sm text-surface-600 dark:text-surface-300">
            <thead class="bg-surface-100 dark:bg-surface-800 text-left text-xs font-medium uppercase">
                <tr>
                    <th scope="col" class="py-3 px-4">{{ __('mcp.name') }}</th>
                    <th scope="col" class="py-3 px-4">{{ __('mcp.scopes') }}</th>
                    <th scope="col" class="py-3 px-4">{{ __('mcp.access_level') }}</th>
                    <th scope="col" class="py-3 px-4 text-right">{{ __('mcp.call_count') }}</th>
                    <th scope="col" class="py-3 px-4">{{ __('mcp.last_used') }}</th>
                    <th scope="col" class="py-3 px-4 text-right">{{ __('mcp.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @if(empty($tokens))
                <tr>
                    <td colspan="6" class="py-6 text-center text-surface-400 dark:text-surface-500">{{ __('mcp.no_tokens') }}</td>
                </tr>
            @else
                @foreach($tokens as $token)
                    <tr class="border-b dark:border-surface-700">
                        <td class="py-3 px-4 font-medium">{{ $token->name }}</td>
                        <td class="py-3 px-4">
                            @foreach($token->scopes ?? [] as $scope)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-surface-200 text-surface-700 dark:bg-surface-700/30 dark:text-surface-300">{{ $scope }}</span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4">
                            @if($token->access_level === 'write')
                                <span class="text-red-600 text-xs font-medium">Write</span>
                            @else
                                <span class="text-surface-500 text-xs font-medium">Read</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $token->call_count }}</td>
                        <td class="py-3 px-4">
                            @if($token->last_used_at)
                                {{ $token->last_used_at->diffForHumans() }}
                            @else
                                <span class="text-surface-400 never-used">{{ __('mcp.never_used') }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            {{-- Widerruf-Button --}}
                            <form method="POST" action="{{ route('mcp.tokens.destroy', $token) }}"
                                  onsubmit="return confirm('{{ __('mcp.revoke_confirm') }}')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded bg-red-500 text-red-600 text-xs font-medium px-2 py-1">
                                    {{ __('mcp.revoke') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>

    {{-- Neuen Token erstellen (einfaches Formular) --}}
    <div class="mt-4 p-4 rounded-lg border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-900">
        <h4 class="font-medium text-surface-900 dark:text-white mb-3">{{ __('mcp.create_token') }}</h4>
        <form method="POST" action="{{ route('mcp.tokens.store') }}" class="space-y-3">
            @csrf
            <div>
                <x-input type="text" placeholder="Token-Name" wire:model="newTokenName"
                         class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"/>
            </div>
            <div>
                <x-input type="text" placeholder="Bereich (z. B. container:read, note:write)"
                         wire:model="newTokenScopes"
                         class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"/>
            </div>
            <div class="flex">
                <select wire:model="newTokenAccessLevel" class="rounded-lg border border-surface-300 bg-white px-3 py-2 text-surface-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    <option value="read">{{ __('mcp.read') }}</option>
                    <option value="write">{{ __('mcp.write') }}</option>
                </select>
                <button type="submit"
                        class="ml-2 rounded bg-indigo-500 px-4 py-2 font-medium text-white hover:bg-indigo-600">
                    {{ __('mcp.create') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Token revoke Bestätigung (already handled by form onsubmit confirm)
</script>