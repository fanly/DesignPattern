<?php

namespace App\Livewire\Pages;

use App\Models\PatternCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
class CategoryShow extends Component
{
    public PatternCategory $category;
    
    public function mount($slug)
    {
        $this->category = PatternCategory::where('slug', $slug)->firstOrFail();
    }
    
    #[Title('分类详情')]
    public function render()
    {
        $patterns = $this->category->designPatterns()
            ->orderBy('name_zh')
            ->get();
            
        return view('livewire.pages.category-show', [
            'category' => $this->category,
            'patterns' => $patterns,
        ]);
    }
}