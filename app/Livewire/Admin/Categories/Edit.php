<?php

namespace App\Livewire\Admin\Categories;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Category $category;
    
    public $title;
    public $title_en;
    public $description;
    public $slug;

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->title = $category->title;
        $this->title_en = $category->title_en;
        $this->description = $category->description;
        $this->slug = $category->slug;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:categories,slug,' . $this->category->id,
        ]);

        $this->category->update([
            'title' => $this->title,
            'title_en' => $this->title_en,
            'description' => $this->description,
            'slug' => $this->slug,
        ]);

        session()->flash('message', '分类更新成功！');
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
        return view('livewire.admin.categories.edit');
    }
}