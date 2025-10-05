<?php

namespace App\Livewire\Pages;

use App\Models\PatternCategory;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('设计模式分类')]
class CategoryIndex extends Component
{
    public function render()
    {
        $categories = PatternCategory::orderBy('name_zh')->get();
        
        return view('livewire.pages.category-index', [
            'categories' => $categories,
        ]);
    }
}