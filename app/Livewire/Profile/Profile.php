<?php

namespace App\Livewire\Profile;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Mein Profil')]
class Profile extends Component
{
    public string $name = '';
    public string $email = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
    }

    public function render()
    {
        return view('livewire.profile.profile');
    }
}
