<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public function toggle()
    {
        $user = auth()->user();
        $next = ($user?->theme ?? session('theme', 'dark')) === 'dark' ? 'light' : 'dark';

        session(['theme' => $next]);

        if ($user) {
            $user->theme = $next;
            $user->save();
        }
    }

    public function render()
    {
        return view('livewire.theme-toggle', [
            'dark' => (auth()->user()?->theme ?? session('theme', 'dark')) === 'dark',
        ]);
    }
}
