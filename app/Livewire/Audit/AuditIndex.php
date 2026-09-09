<?php

namespace App\Livewire\Audit;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Audit-Logs')]
class AuditIndex extends Component
{
    public function render()
    {
        $logs = AuditLog::with('user')->latest()->paginate(20);

        return view('livewire.audit.audit-index', ['logs' => $logs]);
    }
}
