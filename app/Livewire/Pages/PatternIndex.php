<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\PatternCategory;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PatternIndex extends Component
{
    public function render()
    {
        return view('livewire.pages.pattern-index', [
            'categories' => PatternCategory::with('designPatterns')
                ->orderBy(app()->getLocale() === 'zh' ? 'name_zh' : 'name_en')
                ->get()
        ]);
    }
}