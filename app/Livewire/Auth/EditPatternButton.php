<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\DesignPattern;

class EditPatternButton extends Component
{
    public DesignPattern $pattern;

    public function mount(DesignPattern $pattern)
    {
        $this->pattern = $pattern;
    }

    public function edit()
    {
        return redirect()->route('admin.patterns.edit', $this->pattern);
    }

    public function render()
    {
        return view('livewire.auth.edit-pattern-button');
    }
}
