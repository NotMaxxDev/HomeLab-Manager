<?php

namespace App\Livewire\Tunnel;

use App\Models\TunnelConfig;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tunnel-Einstellungen')]
class TunnelSettings extends Component
{
    public function render()
    {
        $tunnels = TunnelConfig::latest()->get();

        return view('livewire.tunnel.tunnel-settings', ['tunnels' => $tunnels]);
    }
}
