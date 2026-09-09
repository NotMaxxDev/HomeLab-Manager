<?php

namespace App\Livewire\Mcp;

use App\Models\McpToken;
use App\Services\Agent\ToolRegistry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('MCP-Tokens')]
class TokenIndex extends Component
{
    public array $tokens = [];

    public function render(ToolRegistry $tools)
    {
        $this->tokens = McpToken::latest()->get();

        return view('livewire.mcp.tokens');
    }
}