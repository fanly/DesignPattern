<?php

namespace App\Livewire\Admin\Patterns;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\DesignPattern;
use App\Models\Category;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class Edit extends Component
{
    public DesignPattern $pattern;
    public $categories;
    
    public $title;
    public $title_en;
    public $description;
    public $content;
    public $category_id;
    public $slug;

    public function mount(DesignPattern $pattern)
    {
        $this->pattern = $pattern;
        $this->categories = Category::all();
        
        $this->title = $pattern->title;
        $this->title_en = $pattern->title_en;
        $this->description = $pattern->description;
        $this->content = $pattern->content;
        $this->category_id = $pattern->category_id;
        $this->slug = $pattern->slug;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'slug' => 'required|string|unique:design_patterns,slug,' . $this->pattern->id,
        ]);

        $this->pattern->update([
            'title' => $this->title,
            'title_en' => $this->title_en,
            'description' => $this->description,
            'content' => $this->content,
            'category_id' => $this->category_id,
            'slug' => $this->slug,
        ]);

        session()->flash('message', '设计模式更新成功！');
        return redirect()->route('admin.dashboard');
    }

    public function updatedTitle($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function render()
    {
        return view('livewire.admin.patterns.edit');
    }
}