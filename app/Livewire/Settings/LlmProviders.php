<?php

namespace App\Livewire\Settings;

use App\Models\LlmProvider;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('LLM Provider')]
class LlmProviders extends Component
{
    public function render()
    {
        $providers = LlmProvider::all();

        return view('livewire.settings.llm-providers', ['providers' => $providers]);
    }
}
