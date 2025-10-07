<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\DesignPattern;

#[Layout('layouts.app')]
class PatternShow extends Component
{
    public DesignPattern $pattern;

    public function mount($slug)
    {
        $this->pattern = DesignPattern::with('category')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pages.pattern-show', [
            'pattern' => $this->pattern,
            'tableOfContents' => $this->pattern->getHeadings(),
            'relatedPatterns' => $this->pattern->category
                ->designPatterns()
                ->where('id', '!=', $this->pattern->id)
                ->limit(3)
                ->get()
        ]);
    }
}