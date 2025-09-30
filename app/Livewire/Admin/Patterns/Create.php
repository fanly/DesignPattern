<?php

namespace App\Livewire\Admin\Patterns;

use Livewire\Component;
use App\Models\DesignPattern;
use App\Models\PatternCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $description = '';
    public int $category_id = 0;
    public array $categories = [];
    
    public function mount()
    {
        $this->categories = PatternCategory::all()->mapWithKeys(function ($category) {
            return [$category->id => $category->getNameAttribute()];
        })->toArray();
    }
    
    public function updatedTitle($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
        
        // 实时预览内容更新
        $this->dispatch('contentUpdated', content: $this->content);
    }
    
    public function updatedContent($value)
    {
        // 实时预览内容更新
        $this->dispatch('contentUpdated', content: $value);
    }
    
    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:design_patterns,slug',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:pattern_categories,id',
        ]);
        
        DesignPattern::create($validated);
        
        session()->flash('success', '设计模式创建成功！');
        return redirect()->route('patterns.index');
    }
    
    public function render()
    {
        return view('livewire.admin.patterns.create');
    }
}