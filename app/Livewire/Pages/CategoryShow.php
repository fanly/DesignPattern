<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\PatternCategory;

#[Layout('layouts.app')]
class CategoryShow extends Component
{
    public PatternCategory $category;

    public function mount($slug)
    {
        $this->category = PatternCategory::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pages.category-show', [
            'category' => $this->category,
            'patterns' => $this->category->designPatterns()
                ->orderBy('sort_order')
                ->get()
        ]);
    }
}