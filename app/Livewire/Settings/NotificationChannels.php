<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Benachrichtigungskanäle')]
class NotificationChannels extends Component
{
    public function render()
    {
        return view('livewire.settings.notification-channels');
    }
}
