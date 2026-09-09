<?php

namespace App\Livewire;

use Livewire\Component;

class LocaleSwitcher extends Component
{
    public function switch(string $locale)
    {
        if (! in_array($locale, ['de', 'en'])) {
            return;
        }

        session(['locale' => $locale]);

        if ($user = auth()->user()) {
            $user->locale = $locale;
            $user->save();
        }

        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.locale-switcher', [
            'current' => app()->getLocale(),
        ]);
    }
}
