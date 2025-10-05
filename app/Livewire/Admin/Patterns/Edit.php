<?php

namespace App\Livewire\Admin\Patterns;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\DesignPattern;
use App\Models\PatternCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class Edit extends Component
{
    public DesignPattern $pattern;
    public $categories;
    
    public $name_zh;
    public $name_en;
    public $description_zh;
    public $description_en;
    public $content_zh;
    public $content_en;
    public $category_id;
    public $slug;

    public function mount(DesignPattern $pattern)
    {
        $this->pattern = $pattern;
        $this->categories = PatternCategory::all();
        
        // 从数据库加载基本信息
        $this->name_zh = $pattern->name_zh;
        $this->name_en = $pattern->name_en;
        $this->description_zh = $pattern->description_zh;
        $this->description_en = $pattern->description_en;
        $this->category_id = $pattern->category_id;
        $this->slug = $pattern->slug;
        
        // 从markdown文件加载内容
        $this->content_zh = $pattern->getContent('zh');
        $this->content_en = $pattern->getContent('en');
    }

    public function save()
    {
        $this->validate([
            'name_zh' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_zh' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content_zh' => 'required|string',
            'content_en' => 'required|string',
            'category_id' => 'required|exists:pattern_categories,id',
            'slug' => 'required|string|unique:design_patterns,slug,' . $this->pattern->id,
        ]);

        // 更新数据库基本信息
        $this->pattern->update([
            'name_zh' => $this->name_zh,
            'name_en' => $this->name_en,
            'description_zh' => $this->description_zh,
            'description_en' => $this->description_en,
            'category_id' => $this->category_id,
            'slug' => $this->slug,
        ]);

        // 保存markdown文件内容
        $this->pattern->saveContent($this->content_zh, 'zh');
        $this->pattern->saveContent($this->content_en, 'en');

        session()->flash('message', '设计模式更新成功！');
        return redirect()->route('admin.dashboard');
    }

    public function updatedNameZh($value)
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