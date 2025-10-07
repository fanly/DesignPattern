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
        $cacheKey = 'pattern_index_categories_' . app()->getLocale();
        
        $categories = cache()->remember($cacheKey, 900, function () { // 15分钟
            return PatternCategory::with(['designPatterns' => function($query) {
                $query->where('is_published', true);
            }])
                ->orderBy(app()->getLocale() === 'zh' ? 'name_zh' : 'name_en')
                ->get();
        });
        
        return view('livewire.pages.pattern-index', [
            'categories' => $categories
        ]);
    }
}