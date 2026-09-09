<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Benutzerverwaltung')]
class UserIndex extends Component
{
    public function render()
    {
        $users = User::with('roles')->latest()->get();

        return view('livewire.users.user-index', ['users' => $users]);
    }
}
