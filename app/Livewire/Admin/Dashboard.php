<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\DesignPattern;
use App\Models\Category;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $patternCount = DesignPattern::count();
        $categoryCount = Category::count();
        
        $recentPatterns = DesignPattern::latest()->limit(5)->get();
        
        return view('livewire.admin.dashboard', [
            'patternCount' => $patternCount,
            'categoryCount' => $categoryCount,
            'recentPatterns' => $recentPatterns,
        ]);
    }
}